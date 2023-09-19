<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\Bopis\Model\Email\SimpleSender;
use WolfSellers\Bopis\Model\Email\Identity\SatisfactionSurvey;

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
     * @param $order \Magento\Sales\Model\Order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendSatisfactionSurveyEmail($to, $order) : bool
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
