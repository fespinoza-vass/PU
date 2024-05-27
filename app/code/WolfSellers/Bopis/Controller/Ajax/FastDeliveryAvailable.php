<?php

namespace WolfSellers\Bopis\Controller\Ajax;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Checkout\Model\Session;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use WolfSellers\Bopis\Helper\LiveBoxConfigProvider;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;
use WolfSellers\Bopis\Logger\Logger;

class FastDeliveryAvailable implements HttpGetActionInterface
{
    /** @var int */
    const MINIMUM_SALABLE_QUANTITY = 2;

    /**
     * @param JsonFactory $jsonResultFactory
     * @param Session $_checkout
     * @param LiveBoxConfigProvider $liveBoxConfigProvider
     * @param DynamicTagRules $dynamicTagRules
     * @param Logger $logger
     */
    public function __construct(
        protected JsonFactory           $jsonResultFactory,
        protected Session               $_checkout,
        protected LiveBoxConfigProvider $liveBoxConfigProvider,
        protected DynamicTagRules       $dynamicTagRules,
        protected Logger                $logger
    )
    {
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        // If $available is true, the popup is not displayed.
        $result->setData(['available' => "1"]);

        try {
            if (!$this->liveBoxConfigProvider->isEnabled()) {
                $this->logger->info('LiveBox is off');
                return $result;
            }

            $items = $this->_checkout->getQuote()->getAllVisibleItems();

            if (count($items) < $this->liveBoxConfigProvider->getMinItems()) {
                //$this->logger->info('LiveBox has fewer elements than the minimum.');
                return $result;
            }

            $splitOrder = false;
            $checkPoints = [];

            foreach ($items as $item) {
                $labels = $this->dynamicTagRules->shippingLabelsByProductSku($item->getSku());

                // New rules: If the current cart is a split order, the popup will be shown.
                $fastLabel = boolval($labels['fast']);
                $inStoreLabel = boolval($labels['instore']);

                if ($fastLabel && $inStoreLabel) {
                    // both labels
                    $checkPoint = 3;
                } elseif (!$fastLabel && $inStoreLabel) {
                    // store pickup label only
                    $checkPoint = 2;
                } elseif ($fastLabel && !$inStoreLabel) {
                    // fast shipping label only
                    $checkPoint = 1;
                } elseif (!$fastLabel && !$inStoreLabel) {
                    // Without tags
                    $checkPoint = 0;
                } else {
                    $checkPoint = 0;
                }

                if (!in_array($checkPoint, $checkPoints)) {
                    $checkPoints[] = $checkPoint;
                }
            }

            // If there is more than one combination of tags, it is a split order.
            if (count($checkPoints) > 1) {
                $splitOrder = true;
            }

            // If it is a split order, then the available $hideLiveBox is 0
            if ($splitOrder) {
                $data = ['available' => "0"];
                $result->setData($data);
            } else {
                return $result;
            }

            return $result;
        } catch (\Throwable $error) {
            $this->logger->error('LiveBox error: ' . $error->getMessage());
            return $result;
        }
    }
}
