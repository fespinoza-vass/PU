<?php

namespace adobe\adobeSignCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class AgreementVars implements ConfigProviderInterface
{
    protected $adobeSignApiService;

    public function __construct(
        \adobe\adobeSignCheckout\Service\AdobeSignApiService $adobeSignApiService
    ) {
        $this->adobeSignApiService = $adobeSignApiService;
    }

    public function getConfig()
    {
        $additionalVariables['accessToken'] = $this->adobeSignApiService->getAccessToken();
        $additionalVariables['agreementStatusUrl'] = $this->adobeSignApiService->getAgreementStatusUrl();
        return $additionalVariables;
    }

}
