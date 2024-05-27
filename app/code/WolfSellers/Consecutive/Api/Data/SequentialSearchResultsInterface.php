<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Api\Data;

interface SequentialSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Sequential list.
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \WolfSellers\Consecutive\Api\Data\SequentialInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

