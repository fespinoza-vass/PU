<?php

namespace WolfSellers\EnvioRapido\Model;

use Psr\Log\LoggerInterface;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\EnvioRapido\Model\Configuration;


abstract class SavarApi extends \Magento\Framework\DataObject
{
    protected LoggerInterface $logger;

    protected Curl $curl;
    private Json $json;

    protected Configuration $configuration;


    public function __construct(
        LoggerInterface $logger,
        Curl            $curl,
        Json            $json,
        Configuration   $configuration,
        array           $data = []
    )
    {
        $this->logger = $logger;
        $this->curl = $curl;
        $this->json = $json;
        $this->configuration = $configuration;
        parent::__construct($data);
    }

    /**
     * @param $data
     * @return string
     */
    public function execute($data)
    {
        $this->sentRequest($data);

        return $this->getResponse();
    }

    /**
     * @return void
     */
    protected function sentRequest($data)
    {
        $this->prepareClient();
        $this->curl->{$this->getMethodType()}($this->getUri(), $this->getRequest($data));
    }

    /**
     * @param $data
     * @return mixed
     */
    abstract protected function getRequest($data);

    /**
     * @return string
     */
    protected function getResponse()
    {
        $response = '{}';
        $body = $this->curl->getBody();

        if ($body) {
            $response = $body->getContent();
        }

        return $this->json->unserialize($response);
    }

    /**
     * @return mixed
     */
    protected abstract function getUri();

    /**
     * @return mixed
     */
    protected abstract function getMethodType();

    /**
     * @return mixed
     */
    private function getBaseUrl()
    {
        $baseUrl = $this->configuration->getProductionWsUrl();

        if ($this->configuration->isSandboxMode()) {
            $baseUrl = $this->configuration->getSandboxWsUrl();
        }

        return $baseUrl;
    }

    /**
     * @return void
     */
    private function prepareClient()
    {
        $token = $this->configuration->getProductionToken();

        if ($this->configuration->isSandboxMode()) {
            $token = $this->configuration->getSandboxToken();
        }

        $this->curl->addHeader('Authorization', sprintf('Bearer %s', $token));
    }
}
