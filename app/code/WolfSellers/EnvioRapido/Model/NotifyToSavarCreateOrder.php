<?php

namespace WolfSellers\EnvioRapido\Model;

use Psr\Log\LoggerInterface;

use WolfSellers\EnvioRapido\Model\SavarApiCreateOrder;

/**
 *
 */
class NotifyToSavarCreateOrder extends SavarApiCreateOrder
{

    /**
     *
     */
    CONST METHOD_TYPE = "post";

    /**
     * @param $data
     * @return mixed
     */
    protected function getRequest($data)
    {
        return $data;
    }

    /**
     * @return mixed
     */
    protected function getUri()
    {
        return $this->getBaseUrl();
    }

    /**
     * @return string
     */
    protected function getMethodType()
    {
        return self::METHOD_TYPE;
    }
}
