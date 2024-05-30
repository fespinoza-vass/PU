<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\SerializerInterface;

class Inventory extends AbstractHelper
{

    const INVENTORY_ENDPOINT = "api/inventory?";
    const USER = "";
    const PASSWORD = "";
    const BASE_URL = "bopis/api/base_url";

    private Curl $curl;
    private SerializerInterface $serializer;
    private $baseUrl = "";
    private ScopeConfigInterface $storeConfig;

    public function __construct(
        Context $context,
        Curl $curl,
        SerializerInterface $serializer,
        ScopeConfigInterface $storeConfig
    )
    {
        parent::__construct($context);
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->storeConfig = $storeConfig;
        $this->baseUrl = $this->storeConfig->getValue(self::BASE_URL);
    }

    public function getCartInventory($skus, $store){
        $params = "skus=" . urlencode(implode(",", $skus)) . "&sources=" . urlencode($store);
        return $this->makeRequest($params);
    }

    protected function makeRequest($params){
        try{
            $this->curl->get($this->baseUrl . self::INVENTORY_ENDPOINT . $params);
        }catch (\Exception $exception){
            return [];
        }

        return $this->serializer->unserialize($this->curl->getBody());
    }

}
