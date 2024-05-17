<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Api\Data;

interface OrderBopisInterface
{
    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getStore();

    /**
     * @param $store
     * @return mixed
     */
    public function setStore($store);
}
