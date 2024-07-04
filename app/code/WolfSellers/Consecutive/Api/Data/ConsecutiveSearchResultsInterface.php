<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Api\Data;

interface ConsecutiveSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get consecutive list.
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface[]
     */
    public function getItems();

    /**
     * Set consecutive_number list.
     * @param \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

