<?php

namespace adobe\adobeSignCheckout\Observer;

class CheckoutSubmitAllAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $logger;
    protected $quoteRepository;
    protected $adobeSignApiService;
    protected $orderRepository;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \adobe\adobeSignCheckout\Service\AdobeSignApiService $adobeSignApiService,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Model\OrderRepository $orderRepository
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->adobeSignApiService = $adobeSignApiService;
        $this->orderRepository = $orderRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getData('quote');
        $order = $observer->getEvent()->getData('order');

        $this->logger->debug('update agreement status started for quote id : ' . $quote->getId());

        if (!$this->adobeSignApiService->isSignAgreementRequired($quote)) {
            $this->logger->debug('no agreement found');
            return $this;
        }

        // get agreement status
        $agreementId = $quote->getData('agreement_id');
        $this->logger->debug('agreement id:' . $agreementId);
        $status = $this->adobeSignApiService->getAgreementSignStatus($agreementId);
        $this->logger->debug('agreement status:' . $status);
        if (empty($status)) {
            $this->logger->warning('Failed to get agreement status');
            return;
        }
        // update quote
        $this->updateQuote($quote->getId(), $status);

        //update order
        $this->logger->debug('order id : ' . $order->getId());
        $this->updateOrder($order->getId(), $agreementId, $status, $quote->getData('sign_url'));

        $this->logger->debug('update agreement status and order table done for quote id : ' . $quote->getId());
        return $this;
    }

    private function updateQuote($id, $status)
    {
        $quoteObj = $this->quoteRepository->get($id);
        $quoteObj->setData('sign_status', $status);
        $this->quoteRepository->save($quoteObj);
    }

    private function updateOrder($orderId, $agreementId, $status, $url)
    {
        $this->logger->debug('update order table for order id : ' . $orderId);
        $orderObj = $this->orderRepository->get($orderId);
        $orderObj->setData('agreement_id', $agreementId);
        $orderObj->setData('sign_status', $status);
        $orderObj->setData('sign_url', $url);
        $this->orderRepository->save($orderObj);
    }
}
