<?php

namespace WolfSellers\Bopis\Plugin\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\OrderRepository;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Api\Data\OrderBopisInterfaceFactory;

class OrderRepositoryPlugin
{
    private BopisRepositoryInterface $bopisRepository;
    private OrderExtensionFactory $orderExtensionFactory;
    private OrderBopisInterfaceFactory $orderBopisFactory;

    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        BopisRepositoryInterface $bopisRepository,
        OrderBopisInterfaceFactory $orderBopisFactory
    )
    {
        $this->bopisRepository = $bopisRepository;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderBopisFactory = $orderBopisFactory;
    }

    public function afterGetList(
        OrderRepository $subject,
        $result
    ) {
        foreach ($result->getItems() as $order) {
            $extensionAttributes = $order->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->orderExtensionFactory->create();
            }
            $bopisData = $this->orderBopisFactory->create();

            try {
                $bopis = $this->bopisRepository->getByQuoteId($order->getQuoteId());
                $bopisData->setStore($bopis->getStore());
                $bopisData->setType($bopis->getType());
            } catch (LocalizedException $e) {
                $bopisData->setStore("");
                $bopisData->setType("");
            }

            $extensionAttributes->setBopis($bopisData);
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $result;
    }
}
