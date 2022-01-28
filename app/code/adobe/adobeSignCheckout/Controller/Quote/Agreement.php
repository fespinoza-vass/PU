<?php

namespace adobe\adobeSignCheckout\Controller\Quote;
use Magento\Framework\Controller\ResultFactory;

class Agreement extends \Magento\Framework\App\Action\Action
{
    protected $quoteRepository;
    protected $request;
    protected $adobeSignApiService;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
            \Magento\Framework\App\Request\Http $request,
            \adobe\adobeSignCheckout\Service\AdobeSignApiService $adobeSignApiService
    ) {
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context);
        $this->request = $request;
        $this->adobeSignApiService = $adobeSignApiService;
    }

    /**
     * @return json
     */
    public function execute()
    {
        $id = $this->request->getParam('id');
        $quote = $this->quoteRepository->get($id);

        $signUrl = "";
        $agreementId = $this->adobeSignApiService->createAgreement($quote);
        if (!empty($agreementId)) {
            $signUrl = $this->resolveSigningUrl($agreementId);

            $quote->setData('agreement_id', $agreementId);
            $quote->setData('sign_status', false);
            $quote->setData('sign_url', $signUrl);
            $this->quoteRepository->save($quote);
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(["agreementId" => $agreementId, "signUrl" => $signUrl]);
        return $resultJson;
    }

    private function resolveSigningUrl($agreementId, $attempt = 0) {
        $maxAttempts = 30;

        $signUrl = $this->adobeSignApiService->getSigningUrl($agreementId);

        if (empty($signUrl) && $attempt < $maxAttempts) {
            sleep(1);
            return $this->resolveSigningUrl($agreementId, ++$attempt);
        }

        return $signUrl;
    }
}
