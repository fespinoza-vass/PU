<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Plugin\Sales\Model\ResourceModel;

use Amasty\Rewards\Model\Order\EarningProcessor;
use Amasty\Rewards\Model\Order\EarningValidator;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model\ResourceModel\Order as SalesOrderResource;

class Order
{
    /**
     * For earn rewards
     */
    public const NOT_AVAILABLE_ACTION = EarningValidator::ADD_COMMENT_ACTION;

    /**
     * @var EarningProcessor
     */
    private $earningProcessor;

    /**
     * @var string
     */
    private $orderStatus;

    /**
     * @var EarningValidator
     */
    private $earningValidator;

    public function __construct(
        EarningProcessor $earningProcessor,
        EarningValidator $earningValidator
    ) {
        $this->earningProcessor = $earningProcessor;
        $this->earningValidator = $earningValidator;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(SalesOrderResource $subject, AbstractModel $order): void
    {
        $this->orderStatus = $order->getOrigData('status');
    }

    /**
     * @param SalesOrderResource $subject
     * @param SalesOrderResource $result
     * @param SalesOrder $order
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        SalesOrderResource $subject,
        SalesOrderResource $result,
        AbstractModel $order
    ): SalesOrderResource {
        if ($this->orderStatus !== $order->getStatus()
            && $this->earningValidator->canEarn($order)
        ) {
            $this->earningProcessor->process($order);
        }

        return $result;
    }
}
