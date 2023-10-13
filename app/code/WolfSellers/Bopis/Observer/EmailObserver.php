<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Helper\RealStates;

class EmailObserver implements ObserverInterface
{
    /** @var BopisRepositoryInterface */
    private BopisRepositoryInterface $bopisRepository;

    /** @var RealStates */
    private RealStates $realStates;

    /**
     * @param BopisRepositoryInterface $bopisRepository
     * @param RealStates $realStates
     */
    public function __construct(
        BopisRepositoryInterface $bopisRepository,
        RealStates               $realStates
    )
    {
        $this->bopisRepository = $bopisRepository;
        $this->realStates = $realStates;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    )
    {
        /** @var \Magento\Sales\Model\Order $order */
        $transport = $observer->getEvent()->getTransport();
        $order = $transport->getOrder();
        try {
            $transport['shipping_method_name'] = $this->realStates->getShippingMethodTitle($order->getShippingMethod());
            $transport['shipping_method_image'] = $order->getShippingMethod();

            $bopis = $this->bopisRepository->getByQuoteId($order->getQuoteId());

            if(strpos($order->getShippingMethod(), "bopis") !== false && $bopis->getType() == "store-pickup") {
                $transport['is_bopis'] = true;
            }
        } catch (\Exception $exception) {}
    }
}
