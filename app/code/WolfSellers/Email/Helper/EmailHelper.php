<?php

declare(strict_types=1);

namespace WolfSellers\Email\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use WolfSellers\Email\Model\Email\Identity\SatisfactionSurvey;
use WolfSellers\Email\Model\Email\SimpleSender;

class EmailHelper
{
    /**
     * @param SimpleSender $simpleSender
     * @param SatisfactionSurvey $satisfactionSurvey
     */
    public function __construct(
        protected SimpleSender $simpleSender,
        protected SatisfactionSurvey $satisfactionSurvey
    ) {
    }

    /**
     * @param $to
     * @param $order Order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendSatisfactionSurveyEmail($to, Order $order) : bool
    {
        $surveyEmail = $this->satisfactionSurvey;

        if (!$surveyEmail->isEnabled()) {
            return false;
        }

        $vars = [
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
            'created_at_formatted' => $order->getCreatedAtFormatted(2),
            'satisfaction_survey_url' => $surveyEmail->getSatisfactionSurveyUrl()
        ];

        $sender = $this->simpleSender;
        $sender->setTemplateIdentifier($surveyEmail->getTemplateId());
        $sender->setSender($surveyEmail->getEmailIdentity());
        $sender->send($to, $vars);
        return true;
    }
}

