<?php

declare(strict_types=1);

namespace WolfSellers\Email\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Sales\Model\OrderFactory;
use WolfSellers\Email\Model\Email\Identity\SatisfactionSurvey;
use WolfSellers\Email\Model\Email\Identity\PreparedOrder;
use WolfSellers\Email\Model\Email\Identity\ShipOrder;
use WolfSellers\Email\Model\Email\Identity\ReadyToPickupOrder;
use WolfSellers\Email\Model\Email\SimpleSender;

class EmailHelper
{
    /**
     * @param SimpleSender $simpleSender
     * @param SatisfactionSurvey $satisfactionSurvey
     * @param PreparedOrder $preparedOrder
     * @param ShipOrder $shipOrder
     * @param ReadyToPickupOrder $readyToPickupOrder
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        protected SimpleSender       $simpleSender,
        protected SatisfactionSurvey $satisfactionSurvey,
        protected PreparedOrder      $preparedOrder,
        protected ShipOrder          $shipOrder,
        protected ReadyToPickupOrder $readyToPickupOrder,
        protected OrderFactory       $orderFactory
    )
    {
    }

    /**
     * @param $to
     * @param $order Order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendSatisfactionSurveyEmail($to, Order $order): bool
    {
        $surveyEmail = $this->satisfactionSurvey;

        if (!$surveyEmail->isEnabled()) {
            return false;
        }

        $vars = $this->getGeneralVars($order);

        $sender = $this->simpleSender;
        $sender->setTemplateIdentifier($surveyEmail->getTemplateId());
        $sender->setSender($surveyEmail->getEmailIdentity());
        $sender->send($to, $vars);
        return true;
    }

    /**
     * @param $to
     * @param Order $order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendPreparedOrderEmail($to, Order $order): bool
    {
        $preparedOrder = $this->preparedOrder;

        if (!$preparedOrder->isEnabled()) {
            return false;
        }

        $vars = $this->getGeneralVars($order);

        $sender = $this->simpleSender;
        $sender->setTemplateIdentifier($preparedOrder->getTemplateId());
        $sender->setSender($preparedOrder->getEmailIdentity());
        $sender->send($to, $vars);
        return true;
    }

    /**
     * @param $to
     * @param ModelOrder $order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendShipOrderEmail($to, Order $order): bool
    {
        $shipOrder = $this->shipOrder;

        if (!$shipOrder->isEnabled()) {
            return false;
        }

        $vars = $this->getGeneralVars($order);

        $sender = $this->simpleSender;
        $sender->setTemplateIdentifier($shipOrder->getTemplateId());
        $sender->setSender($shipOrder->getEmailIdentity());
        $sender->send($to, $vars);
        return true;
    }

    /**
     * @param $to
     * @param ModelOrder $order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendReadyToPickupOrderEmail($to, Order $order): bool
    {
        $readyToPickupOrder = $this->readyToPickupOrder;

        if (!$readyToPickupOrder->isEnabled()) {
            return false;
        }

        $vars = $this->getGeneralVars($order);

        $sender = $this->simpleSender;
        $sender->setTemplateIdentifier($readyToPickupOrder->getTemplateId());
        $sender->setSender($readyToPickupOrder->getEmailIdentity());
        $sender->send($to, $vars);
        return true;
    }

    /**
     * @param ModelOrder $order
     * @return array
     */
    public function getGeneralVars(Order $order): array
    {
        return [
            'order' => $order,
            'order_id' => $order->getId(),
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->simpleSender->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->simpleSender->getFormattedBillingAddress($order),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ],
            'created_at_formatted' => $order->getCreatedAtFormatted(2)
        ];
    }

    /**
     * @param $order
     * @return ModelOrder
     */
    public function getOrderModel($order)
    {
        return $this->orderFactory->create()->loadByIncrementId($order->getIncrementId());
    }
}

