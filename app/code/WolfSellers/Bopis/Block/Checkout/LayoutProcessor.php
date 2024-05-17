<?php

namespace WolfSellers\Bopis\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use WolfSellers\Bopis\Helper\BopisAddress;

class LayoutProcessor  implements LayoutProcessorInterface
{
    private BopisAddress $bopisAddressHelper;

    public function __construct(
        BopisAddress $bopisAddressHelper
    )
    {
        $this->bopisAddressHelper = $bopisAddressHelper;
    }

    public function process($jsLayout)
    {
        $this->bopisAddressHelper->fillAddress($jsLayout);

        return $jsLayout;
    }
}
