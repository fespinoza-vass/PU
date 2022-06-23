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
    private const GENERATE_LABEL_URI = 'ge';
    private const TRACKING_URI = 'tracking/';

    /** @var string Api Action */
    private string $action = '';

    /** @var string Last response error */
    private string $lastError = '';

    /** @var array Authentication data */
    private array $auth;

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

        return $this->post(self::QUOTE_URI, $payload);
    }

    /**
     * Generate label.
     *
     * @param array $data
     *
     * @return array
     */
    public function generateLabel(array $data): array
    {
        $payload = $data;
        $this->action = 'Generate label request';

        return $this->post(self::GENERATE_LABEL_URI, $payload);
    }

    /**
     * Get tracking info.
     *
     * @param array $data
     *
     * @return array
     */
    public function getTrackingInfo(array $data): array
    {
        $payload = $data;
        $this->action = 'Get tracking info request';

        return $this->get(self::TRACKING_URI, $payload);
    }

    /**
     * Get last error for request.
     *
     * @return string
     */
    public function getLastError(): string
    {
        return $this->lastError;
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

        return new Client([
            'base_uri' => $this->getBaseUrl(),
            'headers' => $headers,
            'verify' => $this->config->sslVerify(),
        ]);
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
        $sqlError = (int) ($result[0]['sql_error'] ?? 0);
        $error = (int) ($result['error'] ?? 0);

        if ($error < 0 || $sqlError < 0) {
            $message = null;

            if ($error < 0) {
                $message = $result['mensaje'] ?? '';
                $message .= sprintf(' (%s)', $error);
            }

            if ($sqlError < 0) {
                $message = $result[0]['msg_error'] ?? '';
                $message .= sprintf(' (%s)', $sqlError);
            }

            throw new \Exception($message ?? __('Error processing response: %1', $rawResponse));
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
        $this->lastError = $exception->getMessage();

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

    /**
     * Get shortcut.
     *
     * @param string $uri
     * @param array $payload
     * @param bool $auth
     * @param array $options
     *
     * @return array
     */
    private function get(string $uri, array $payload, bool $auth = true, array $options = []): array
    {
        $result = [];

        try {
            $data = [
                'json' => json_encode($payload),
            ];

            $uri .= '?'.http_build_query($data);

            $this->log([
                'base_uri' => $this->getBaseUrl(),
                'uri' => $uri,
                'method' => 'GET',
                'payload' => $payload,
                'auth' => $this->auth,
            ]);

            $this->response = $this->getClient($auth)->get($uri, $options);

            $result = $this->parseResponse();
        } catch (\Exception $exception) {
            $this->parseException($exception);
        }

        return $result;
    }
}
