<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Helper\RealStates;
use WolfSellers\Email\Helper\EmailHelper;

class EmailObserver implements ObserverInterface
{
    /** @var BopisRepositoryInterface */
    private BopisRepositoryInterface $bopisRepository;

    /** @var RealStates */
    private RealStates $realStates;

    /** @var EmailHelper */
    private EmailHelper $emailHelper;

    /**
     * @param BopisRepositoryInterface $bopisRepository
     * @param RealStates $realStates
     * @param EmailHelper $emailHelper
     */
    public function __construct(
        BopisRepositoryInterface $bopisRepository,
        RealStates               $realStates,
        EmailHelper              $emailHelper
    )
    {
        $this->bopisRepository = $bopisRepository;
        $this->realStates = $realStates;
        $this->emailHelper = $emailHelper;
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
            $transport['delivery'] = $this->emailHelper->getDeliveryType($order);

            $bopis = $this->bopisRepository->getByQuoteId($order->getQuoteId());

            if(strpos($order->getShippingMethod(), "bopis") !== false && $bopis->getType() == "store-pickup") {
                $transport['is_bopis'] = true;
            }
        } catch (\Exception $exception) {}
    }
}
