<?php

namespace WolfSellers\Bopis\ViewModel;

use WolfSellers\Bopis\Helper\RealStates;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class GeneralOrder implements ArgumentInterface
{
    /**
     * @param RealStates $_realStates
     */
    public function __construct(
        protected RealStates $_realStates
    )
    {
    }

    /**
     * @param $shippingMethodCode
     * @return string
     */
    public function getShippingMethodTitle($shippingMethodCode)
    {
        return $this->_realStates->getShippingMethodTitle($shippingMethodCode);
    }

    /**
     * @param $status
     * @return string|null
     */
    public function getStateLabel($status): ?string
    {
        if (!$status) {
            return $status;
        }
        return $this->_realStates->getStateLabel($status);
    }


}
