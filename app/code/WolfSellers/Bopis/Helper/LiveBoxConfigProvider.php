<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class LiveBoxConfigProvider extends AbstractHelper
{
    /** @var string */
    const XPATH_IS_ENABLED = 'bopis/live_Box/enabled';

    /** @var string */
    const XPATH_MIN_ITEMS = 'bopis/live_Box/min_items';


    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XPATH_IS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getMinItems(): mixed
    {
        return $this->scopeConfig->getValue(self::XPATH_MIN_ITEMS, ScopeInterface::SCOPE_STORE);
    }
}
