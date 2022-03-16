<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-03-09
 * Time: 12:39
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use WolfSellers\Urbano\Helper\Config;

/**
 * Api Service Client.
 */
class ApiService
{
    private const QUOTE_URI = 'cotizarenvio';

    private string $action = '';

    private array $auth;

    /** @var Client */
    private Client $client;

    /** @var ResponseInterface */
    private ResponseInterface $response;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var Config */
    private Config $config;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;

        $this->auth = [
            'user' => $this->config->getUser(),
            'pass' => $this->config->getPassword(),
        ];
    }

    /**
     * Get quotes.
     *
     * @param array $data
     *
     * @return array
     */
    public function getQuotes(array $data): array
    {
        $payload = $data;
        $this->action = 'Get quote request';

        return $this->post(self::QUOTE_URI, $payload, true);
    }

    /**
     * Get api client.
     *
     * @param bool $auth
     *
     * @return Client
     */
    private function getClient(bool $auth = true): Client
    {
        $headers = [];

        if ($auth) {
            $headers = $this->auth;
        }

        $this->client = new Client([
            'base_uri' => $this->getBaseUrl(),
            'headers' => $headers,
        ]);


        return $this->client;
    }

    /**
     * Parse response.
     *
     * @return array
     *
     * @throws \Exception
     */
    private function parseResponse(): array
    {
        $rawResponse = $this->response->getBody()->getContents();
        $this->log(['response' => $rawResponse], 'Response raw.');

        $result = json_decode($rawResponse, true);

        $error = $result[0]['msg_error'] ?? false;

        if ($error) {
            if ($code = $result[0]['sql_error'] ?? false) {
                $error .= sprintf(' (%s)', $code);
            }

            throw new \Exception($error ?? __('Error processing response: %1', $rawResponse));
        }

        return $result;
    }

    /**
     * Parse exception.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    private function parseException(\Exception $exception)
    {
        $this->logger->error('Urbano API Error.', [
            'status' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ]);
    }

    /**
     * Base url for webservice.
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return $this->config->isSandbox() ? $this->config->getSandboxWsUrl() : $this->config->getProductionWsUrl();
    }

    /**
     * Log.
     *
     * @param array $context
     * @param string|null $message
     *
     * @return void
     */
    private function log(array $context = [], ?string $message = null)
    {
        if (!$message) {
            $message = $this->action;
        }

        $message = 'Urbano API: '.$message;

        // todo: Check if debug is active.
        $this->logger->info($message, $context);
    }

    /**
     * Post shortcut.
     *
     * @param string $uri
     * @param array $payload
     * @param bool $auth
     *
     * @param array $options
     *
     * @return array
     */
    private function post(string $uri, array $payload, bool $auth = true, array $options = []): array
    {
        $result = [];

        try {
            $this->log([
                'base_uri' => $this->getBaseUrl(),
                'uri' => $uri,
                'method' => 'POST',
                'payload' => $payload,
                'auth' => $this->auth,
            ]);

            if (!empty($payload)) {
                $options['form_params'] = ['json' => json_encode($payload)];
            }

            $this->response = $this->getClient($auth)->post($uri, $options);

            $result = $this->parseResponse();
        } catch (\Exception $exception) {
            $this->parseException($exception);
        }

        return $result;
    }
}
