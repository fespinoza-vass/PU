<?php

namespace WolfSellers\Checkout\Payment\Plugin;

use Closure;
use PechoSolutions\Visanet\Controller\Visa\Web;
use Magento\Checkout\Model\Session;
use WolfSellers\Checkout\Logger\Logger;

class VisanetWebTracking
{
    /**
     * @var Session
     */
    protected Session $checkoutSession;

    /**
     * @var Logger
     */
    protected Logger $wolfLogger;

    /**
     * @param Session $checkoutSession
     * @param Logger $wolfLogger
     */
    public function __construct(
        Session $checkoutSession,
        Logger  $wolfLogger
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->wolfLogger = $wolfLogger;
    }

    /**
     * @param Web $subject
     * @param Closure $proceed
     * @return mixed|void
     */
    public function aroundExecute(
        Web     $subject,
        Closure $proceed
    )
    {
        try {
            $quote = $this->checkoutSession->getQuote();

            $orderData = [
                'quote_id' => $quote->getId(),
                'customer_id' => $quote->getCustomerId() ?? 'guest',
                'customer_name' => $quote->getCustomerFirstname(),
                'customer_email' => $quote->getCustomerEmail(),
                'delivery_method' => $quote->getShippingAddress()->getShippingMethod(),
                'delivery_postcode' => $quote->getShippingAddress()->getPostcode(),
                'delivery_street' => $quote->getShippingAddress()->getStreet(),
                'payment' => [
                    'method' => $quote->getPayment()->getMethod(),
                    'amount' => $quote->getGrandTotal(),
                    'shipping_amount' => $quote->getShippingAddress()->getShippingAmount()
                ]
            ];

            $this->wolfLogger->info(print_r($orderData, true));

            return $proceed();
        } catch (\Throwable $error) {
            $this->wolfLogger->error($error->getMessage() . $error->getTraceAsString());
        }

    }
}
