<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vass\NiubizVisanet\Model\Payment;

class VassVisanet extends \Magento\Payment\Model\Method\Cc
{
    public const CODE = 'vassvisanet';

    protected $_code = self::CODE;

    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $logger;
    protected $configNiubiz;
    protected $encryptor;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param ProFactory $proFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $requestHttp
     * @param CartFactory $cartFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Vass\NiubizVisanet\Model\Config $configNiubiz,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Paypal\Model\ProFactory $proFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $requestHttp,
        \Magento\Paypal\Model\CartFactory $cartFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_requestHttp = $requestHttp;
        $this->_cartFactory = $cartFactory;
        $this->_logger = $logger;
        $this->_configNiubiz = $configNiubiz;
        $this->encryptor = $encryptor;
    }

    /**
     * Capture Payment.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {

            //check if payment has been authorized
            if(is_null($payment->getParentTransactionId())) {
                $this->authorize($payment, $amount);
            }

            //build array of payment data for API request.
            $request = [
                'capture_amount' => $amount,
                //any other fields, api key, etc.
            ];

            //make API request to credit card processor.
            $response = $this->makeCaptureRequest($request);

            //todo handle response

            //transaction is done.
            $payment->setIsTransactionClosed(1);

        } catch (Exception $e) {
            $this->debug($payment->getData(), $e->getMessage());
        }

        return $this;
    }

    /**
     * Authorize a payment.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {

            ///build array of payment data for API request.
            $request = [
                'cc_type' => $payment->getCcType(),
                'cc_exp_month' => $payment->getCcExpMonth(),
                'cc_exp_year' => $payment->getCcExpYear(),
                'cc_number' => $payment->getCcNumberEnc(),
                'amount' => $amount
            ];

            // $this->_logger->debug('hh');
            //check if payment has been authorized
            $response = $this->makeAuthRequest($request);

        } catch (Exception $e) {
            $this->debug($payment->getData(), $e->getMessage());
        }

        if(isset($response['transactionID'])) {
            // Successful auth request.
            // Set the transaction id on the payment so the capture request knows auth has happened.
            $payment->setTransactionId($response['transactionID']);
            $payment->setParentTransactionId($response['transactionID']);
        }

        //processing is not done yet.
        $payment->setIsTransactionClosed(0);

        return $this;
    }

    /**
     * Set the payment action to authorize_and_capture
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return self::ACTION_AUTHORIZE_CAPTURE;
    }

    /**
     * Test method to handle an API call for authorization request.
     *
     * @param $request
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function makeAuthRequest($request)
    {
        $response = ['transactionId' => 123]; //todo implement API call for auth request.

        if(!$response) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Failed auth request.'));
        }

        return $response;
    }

    /**
     * Test method to handle an API call for capture request.
     *
     * @param $request
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function makeCaptureRequest($request)
    {
        $response = ['success']; //todo implement API call for capture request.

        if(!$response) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Failed capture request.'));
        }

        return $response;
    }
}

