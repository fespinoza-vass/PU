<?php

namespace WolfSellers\OrderQR\Plugin;

use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use WolfSellers\OrderQR\Helper\QR;

/**
 *
 */
class PlaceOrderQRPlugin
{

    /**
     * @var QR
     */
    protected $_qrHelper;
    /**
     *
     */
    CONST SHIPPING_METHOD_FOR_QRCODE = "pickup";

    /**
     * @param QR $qrHelper
     */
    public function __construct(
        QR $qrHelper
    ) {
        $this->_qrHelper = $qrHelper;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function afterPlace(OrderManagementInterface $subject,
                               OrderInterface           $result){

        $order = $result;

        try {
            if($order->getPayment()->getMethod() == self::SHIPPING_METHOD_FOR_QRCODE)
            {
                $this->_qrHelper->generateQR($order->getIncrementId());
            }

        } catch (\Throwable $error) {
            $this->_logger->addError(
                "ERROR AFTERPLACE PLUGIN ". $error->getMessage() . $error->getTraceAsString()
            );
        }
        return $result;
    }
}

