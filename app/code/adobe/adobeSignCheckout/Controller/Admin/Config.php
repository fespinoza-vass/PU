<?php

namespace adobe\adobeSignCheckout\Controller\Admin;
use Magento\Framework\Controller\ResultFactory;

class Config extends \Magento\Framework\App\Action\Action
{

    protected $helperData;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \adobe\adobeSignCheckout\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * @return json
     */
    public function execute()
    {

        $response['apiAccessPoint'] = $this->helperData->getApiAccessPoint();
        $response['applicationId'] = $this->helperData->getApplicationID();
        $response['clientSecret'] = $this->helperData->getClientSecret();
        $response['refreshToken'] = $this->helperData->getRefreshToken();
        $response['senderEmail'] = $this->helperData->getSenderEmail();
        $response['emailTemplate'] = $this->helperData->getEmailTemplate();
        $response['role'] = $this->helperData->getRole();
        $response['authMethod'] = $this->helperData->getAuthMethod();
        $response['password'] = $this->helperData->getPassword();
        $response['json'] = $this->helperData->getJson();
        $response['productCategories'] = $this->helperData->getProdCategories();
        $response['shops'] = $this->helperData->getShops();

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
    }
}
