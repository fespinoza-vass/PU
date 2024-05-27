<?php

declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Api;

interface SourceStockInterface
{
    /**
     * @param string $cartId
     * @param string $sourceCode
     *
     * @return bool
     */
    public function getAvailableStock($cartId, $sourceCode): bool;
}
