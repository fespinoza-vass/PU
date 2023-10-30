<?php

namespace WolfSellers\EnvioRapido\Model;

use Psr\Log\LoggerInterface;

use WolfSellers\EnvioRapido\Model\SavarApiCreateOrder;

class GetSavarOrder extends SavarApiGetOrder
{

    CONST METHOD_TYPE = "get";

    protected function getRequest($data)
    {
        return $data;
    }

    protected function getUri()
    {
        return $this->getBaseUrl();
    }

    protected function getMethodType()
    {
        return self::METHOD_TYPE;
    }
}
