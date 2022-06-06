<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-26
 * Time: 14:36
 */

declare(strict_types=1);

namespace WolfSellers\OrderTracking\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Order Tracking Widget.
 */
class OrderTracking extends Template implements BlockInterface
{
    protected $_template = 'widget/order-tracking.phtml';

}
