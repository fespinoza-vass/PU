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

            // The items are reviewed validating the available labels.
            foreach ($items as $item) {
                $labels = $this->dynamicTagRules->shippingLabelsByProductSku($item->getSku());
                $fastlabelAvailable = boolval($labels['fast']);

                // If any item does not have the "fast label", then the available $fastlabelAvailable is 0
                if (!$fastlabelAvailable) {
                    $data = ['available' => "0"];
                    $result->setData($data);
                    return $result;
                }
            }

            return $result;
        } catch (\Throwable $error) {
            $this->logger->error('LiveBox error: ' . $error->getMessage());
            return $result;
        }
    }
}
