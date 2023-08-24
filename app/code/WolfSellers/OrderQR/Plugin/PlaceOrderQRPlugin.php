<?php

namespace WolfSellers\OrderQR\Plugin;

use AWS\CRT\Log;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use WolfSellers\OrderQR\Helper\QR;
use WolfSellers\OrderQR\Logger\Logger;

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
    CONST SHIPPING_METHOD_FOR_QRCODE = "instore_pickup";

    /** @var Logger */
    protected $_logger;

    /**
     * @param QR $qrHelper
     */
    public function __construct(
        QR $qrHelper,
        Logger $logger

    ) {
        $this->_logger = $logger;
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
            if($order->getShippingMethod() == self::SHIPPING_METHOD_FOR_QRCODE)
            {
                $this->_qrHelper->generateQR($order->getIncrementId());
            }

        } catch (\Throwable $error) {
            $this->_logger->error($error->getMessage());
        }
        return $result;
    }
}

