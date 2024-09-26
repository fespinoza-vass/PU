<?php
/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_AmastyStoreLocator
 * @author Vass Team
 */
declare(strict_types=1);

namespace Vass\AmastyStoreLocator\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Amasty\Storelocator\Model\ConfigProvider;
use Magento\Framework\UrlInterface;

class StoreLocator implements ArgumentInterface
{
    /**
     * Constructor
     *
     * @param Session $customerSession
     */
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * Get Customer data
     *
     * @return CustomerModel|null
     */

    public function getLinkToMap($params = [])
    {
        return $this->urlBuilder->getUrl(
            $this->configProvider->getUrl(),
            ['_query' => $params]
        );
    }

}
