<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Observer\Checkout;

use Laminas\Log\Logger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Model\Queue\NotificationData;
use WolfSellers\Bopis\Model\Queue\NotificationDataFactory;

class SubmitAllAfter implements ObserverInterface
{
    const TOPIC_NAME = 'bopis.order.notifications';
    private PublisherInterface $publisher;
    private BopisRepositoryInterface $bopisRepository;
    private NotificationDataFactory $notificationDataFactory;
    private Json $json;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * SubmitAllAfter constructor.
     * @param PublisherInterface $publisher
     * @param BopisRepositoryInterface $bopisRepository
     */
    public function __construct(
        PublisherInterface $publisher,
        BopisRepositoryInterface $bopisRepository,
        NotificationDataFactory $notificationDataFactory,
        Json $json
    ){
        $this->publisher = $publisher;
        $this->bopisRepository = $bopisRepository;
        $this->notificationDataFactory = $notificationDataFactory;
        $this->json = $json;
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/bopis.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
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
        $order = $observer->getEvent()->getOrder();
        try {
            $bopis = $this->bopisRepository->getByQuoteId($order->getQuoteId());

            if(strpos($order->getShippingMethod(), "bopis") !== false && $bopis->getStore() != "false") {

                /** @var NotificationData $notificationData */
                $notificationData = $this->notificationDataFactory->create();
                $notificationData->setType(NotificationData::NEW_ORDER);
                $notificationData->setOrderId($order->getId());

                $this->publisher->publish(self::TOPIC_NAME, $notificationData);

                $order->setData("bopis_store",$bopis->getStore());
                $order->setData("bopis_type",$bopis->getType());
                $order->save();

            }
        } catch (NoSuchEntityException $exception) {}
    }
}
