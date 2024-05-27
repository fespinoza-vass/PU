<?php

namespace WolfSellers\Checkout\Plugin\Quote\Model;

use Psr\Log\LoggerInterface;

class BillingAddressManagementGuest
{
    protected $logger;
    private \Magento\Sales\Model\Order\AddressRepository $addressRepository;

    /**
     * @param LoggerInterface $logger
     * @param \Magento\Sales\Model\Order\AddressRepository $addressRepository
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order\AddressRepository $addressRepository
    ) {
        $this->logger = $logger;
        $this->addressRepository = $addressRepository;
    }

    public function beforeSavePaymentInformation(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
                                                                    $cartId,
                                                                    $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        try {

            $this->logger->debug("\WolfSellers\Checkout\Plugin\Quote\Model\BillingAddressManagement::beforeSavePaymentInformationAndPlaceOrder");
            if (!empty($billingAddress)){
                $this->logger->debug("\WolfSellers\Checkout\Plugin\Quote\Model\BillingAddressManagement::beforeSavePaymentInformationAndPlaceOrder 2");
                if (!empty($billingAddress->getExtensionAttributes())){
                    $this->logger->debug("\WolfSellers\Checkout\Plugin\Quote\Model\BillingAddressManagement::beforeSavePaymentInformationAndPlaceOrder 3");
                    $extensionAttributes = $billingAddress->getExtensionAttributes();
                    $this->logger->debug($extensionAttributes->getRuc());
                    $this->logger->debug($extensionAttributes->getRazonSocial());
                    $this->logger->debug($extensionAttributes->getDireccionFiscal());
                    $this->logger->debug($extensionAttributes->getInvoiceRequired());
                    $billingAddress->setCustomAttribute('direccion_fiscal',$extensionAttributes->getDireccionFiscal());
                    $billingAddress->setDireccionFiscal($extensionAttributes->getDireccionFiscal());

                    $billingAddress->setCustomAttribute('razon_social',$extensionAttributes->getRazonSocial());
                    $billingAddress->setRazonSocial($extensionAttributes->getRazonSocial());

                    $billingAddress->setCustomAttribute('ruc',$extensionAttributes->getRuc());
                    $billingAddress->setRuc($extensionAttributes->getRuc());

                    $billingAddress->setCustomAttribute('invoice_required',$extensionAttributes->getInvoiceRequired());
                    $billingAddress->setInvoiceRequired($extensionAttributes->getInvoiceRequired());

                }
            }
        }catch (\Exception $exception){
            $this->logger->critical($exception->getMessage());
        }
    }

}
