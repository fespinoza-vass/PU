<?php

namespace WolfSellers\Bopis\ViewModel;

use WolfSellers\Bopis\Helper\RealStates;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProgressBar implements ArgumentInterface
{
    /**
     * @param RealStates $_realStates
     */
    public function __construct(
        protected RealStates $_realStates,
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
     * @return array
     */
    public function getRealBopisStates(): array
    {
        return $this->_realStates->getRealBopisStates();
    }

    /**
     * @return string
     */
    public function getPickupShippingMethodKey()
    {
        return \WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection::PICKUP_SHIPPING_METHOD;
    }
}
