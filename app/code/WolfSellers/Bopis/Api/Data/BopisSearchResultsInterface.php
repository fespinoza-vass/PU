<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BopisSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get bopis list.
     * @return BopisInterface[]
     */
    public function getItems();

    /**
     * Set bopis list.
     * @param BopisInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

