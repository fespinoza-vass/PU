<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Api\Data;

interface DireccionesTiendasSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get DireccionesTiendas list.
     * @return \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface[]
     */
    public function getItems();

    /**
     * Set ubigeo list.
     * @param \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

