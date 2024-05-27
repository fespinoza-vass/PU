<?php
declare(strict_types=1);

namespace WolfSellers\Bopis\Model;

use WolfSellers\Bopis\Api\Data\OrderBopisInterface;

class OrderBopis implements OrderBopisInterface
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $store;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }
}
