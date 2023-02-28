<?php

namespace WolfSellers\SkinCare\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface SimulatorSearchResultsInterface  extends SearchResultsInterface
{
    /**
     * @return SimulatorInterface[]
     */
    public function getItems();

    /**
     * @param SimulatorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
