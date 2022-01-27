<?php

namespace adobe\adobeSignCheckout\Observer;

class Checkoutinit implements \Magento\Framework\Event\ObserverInterface
{
    protected $logger;
    protected $checkoutSession;
    protected $quoteRepository;
    protected $adobeSignApiService;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \adobe\adobeSignCheckout\Service\AdobeSignApiService $adobeSignApiService,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->adobeSignApiService = $adobeSignApiService;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $this->checkoutSession->getQuote();
        $this->logger->debug('prepare agreement started for quote id : ' . $quote->getId());
        if ($this->adobeSignApiService->isSignAgreementRequired($quote)) {
            $this->updateQuote($quote->getId(), "Required", false, "");
        } else {
            $this->logger->debug('sign agreement is not required');
            // the cart may have updated and reset is required
            $this->updateQuote($quote->getId(), "", false, "");
        }

        return $this;
    }

    private function updateQuote($quoteId, $agreementId, $status, $signUrl)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $quote->setData('agreement_id', $agreementId);
        $quote->setData('sign_status', $status);
        $quote->setData('sign_url', $signUrl);
        $this->quoteRepository->save($quote);
    }
}
