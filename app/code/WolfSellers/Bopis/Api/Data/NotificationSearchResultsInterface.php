<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Api\Data;

interface NotificationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Notification list.
     * @return \WolfSellers\Bopis\Api\Data\NotificationInterface[]
     */
    public function getItems();

    /**
     * Set type list.
     * @param \WolfSellers\Bopis\Api\Data\NotificationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
