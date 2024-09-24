<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model\Config\Source;

use Magento\Sales\Model\Config\Source\Order\Status;
use Magento\Sales\Model\Order as OrderModel;

class OrderStatus extends Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [
        OrderModel::STATE_NEW,
        OrderModel::STATE_PROCESSING,
        OrderModel::STATE_COMPLETE,
        OrderModel::STATE_PAYMENT_REVIEW,
        OrderModel::STATE_PENDING_PAYMENT,
    ];

    public function toOptionArray(): array
    {
        $options = parent::toOptionArray();
        //removing "please select" option
        array_shift($options);

        return $options;
    }
}
