<?php

namespace WolfSellers\EnvioRapido\Model;

use Psr\Log\LoggerInterface;

use WolfSellers\EnvioRapido\Model\SavarApiCreateOrder;

class NotifyToSavarCreateOrder extends SavarApiCreateOrder
{

    CONST METHOD_TYPE = "post";

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
