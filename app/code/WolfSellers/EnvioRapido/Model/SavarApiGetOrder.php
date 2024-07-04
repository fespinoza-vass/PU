<?php

namespace WolfSellers\EnvioRapido\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\EnvioRapido\Model\Configuration;
use WolfSellers\EnvioRapido\Logger\Logger;


/**
 *
 */
abstract class SavarApiGetOrder extends \Magento\Framework\DataObject
{
    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var Curl
     */
    protected Curl $curl;
    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var \WolfSellers\EnvioRapido\Model\Configuration
     */
    protected Configuration $configuration;


    /**
     * @param Logger $logger
     * @param Curl $curl
     * @param Json $json
     * @param \WolfSellers\EnvioRapido\Model\Configuration $configuration
     * @param array $data
     */
    public function __construct(
        Logger $logger,
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
     * @return array
     */
    public function execute($data)
    {
        $this->logger->info("REQUEST: ". $this->json->serialize($data));
        try{
            $this->sentRequest($data);

            return $this->getResponse();
        } catch (\Throwable $error){
            $this->logger->error($error->getMessage());
        }
    }

    /**
     * @return void
     */
    protected function sentRequest($data)
    {
        $this->prepareClient();
        $url = str_replace("#",$data,$this->getUri());
        $this->curl->{$this->getMethodType()}($url, $this->getRequest($data));
    }

    /**
     * @param $data
     * @return mixed
     */
    abstract protected function getRequest($data);

    /**
     * @return array
     */
    protected function getResponse()
    {
        $response = $this->curl->getBody();

        $result = [
            'state_code' => $this->curl->getStatus(),
        ];

        $result['response'] = $response ? $this->json->unserialize($response) : "";


        $this->logger->info("RESPONSE: ". $this->json->serialize($result));

        return $result;
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
    public function getBaseUrl()
    {
        $baseUrl = $this->configuration->getProductionStatusEndpoint();

        if ($this->configuration->isSandboxMode()) {
            $baseUrl = $this->configuration->getSandboxStatusEndpoint();
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
