<?php

namespace WolfSellers\Bopis\Model\Order\Email;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{

    /**
     * Prepare and send email message
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendNotificationEmail($email, $username)
    {
        $this->configureEmailTemplate();
        $this->transportBuilder->addTo($email, $username);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
    }

}