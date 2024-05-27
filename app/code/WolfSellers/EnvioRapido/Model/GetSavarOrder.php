<?php

namespace WolfSellers\EnvioRapido\Model;

use Psr\Log\LoggerInterface;

use WolfSellers\EnvioRapido\Model\SavarApiCreateOrder;

/**
 *
 */
class GetSavarOrder extends SavarApiGetOrder
{

    /**
     *
     */
    CONST METHOD_TYPE = "get";

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
