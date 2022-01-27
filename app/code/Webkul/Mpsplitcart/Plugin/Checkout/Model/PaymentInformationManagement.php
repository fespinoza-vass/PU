<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitcart
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsplitcart\Plugin\Checkout\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Cart source
 */
class PaymentInformationManagement
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    public function __construct(
        \Webkul\Mpsplitcart\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * afterGetSectionData
     * updates the result from checkout session
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress
    ) {
        try {
            $result = $this->helper->checkSplitCart();
            $session = $this->helper->getCheckoutRemoveSession();

            if (count($result)>1
                && $this->helper->checkMpsplitcartStatus()
                && (!$session || $session!==1 || $session==null)
            ) {
                throw new CouldNotSaveException(
                    __('Invalid checkout')
                );
            } else {
                return $proceed($cartId, $paymentMethod, $billingAddress);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        } catch (\Exception $e) {
            $this->getLogger()->critical($e);
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        }
    }

    /**
     * Get logger instance
     *
     * @return \Psr\Log\LoggerInterface
     * @deprecated 100.2.0
     */
    public function getLogger()
    {
        if (!$this->logger) {
            $this->logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        }
        return $this->logger;
    }
}
