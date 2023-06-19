<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class EmailObserver implements ObserverInterface
{
    private BopisRepositoryInterface $bopisRepository;

    /**
     * @param BopisRepositoryInterface $bopisRepository
     */
    public function __construct(
        BopisRepositoryInterface $bopisRepository
    ){
        $this->bopisRepository = $bopisRepository;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ) {
        /** @var \Magento\Sales\Model\Order $order */
        $transport = $observer->getEvent()->getTransport();
        $order = $transport->getOrder();
        try {
            $bopis = $this->bopisRepository->getByQuoteId($order->getQuoteId());

            if(strpos($order->getShippingMethod(), "bopis") !== false && $bopis->getType() == "store-pickup") {
                $transport['is_bopis'] = true;
            }
        } catch (\Exception $exception) {}
    }
}
