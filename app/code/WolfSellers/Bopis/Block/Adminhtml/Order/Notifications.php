<?php
namespace WolfSellers\Bopis\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;

class Notifications extends Template
{
    /**
     * Return last order in processing
     *
     * @return int
     */
    public function getLastOrder()
    {
        return 1337;
    }
}
