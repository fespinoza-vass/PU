<?php

namespace WolfSellers\Bopis\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use WolfSellers\Bopis\Helper\RememberMeHelper;

class RememberMe implements ArgumentInterface
{
    /**
     * @param RememberMeHelper $rememberMeHelper
     */
    public function __construct(
        protected RememberMeHelper $rememberMeHelper
    )
    {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->rememberMeHelper->isEnabled();
    }
}
