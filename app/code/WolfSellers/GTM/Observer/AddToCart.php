<?php
namespace WolfSellers\GTM\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use WolfSellers\GTM\Helper\Data as GtmHelper;
use Psr\Log\LoggerInterface;

class AddToCart implements ObserverInterface
{
    protected $registry;
    protected $gtmHelper;
    protected $logger;

    public function __construct(
        Registry $registry,
        GtmHelper $gtmHelper,
        LoggerInterface $logger
    ) {
        $this->registry = $registry;
        $this->gtmHelper = $gtmHelper;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->debug('AddToCart Observer executed');
        $product = $observer->getEvent()->getProduct();
        $request = $observer->getEvent()->getRequest();

        if ($product) {
            $qty = $request->getParam('qty', 1);
            $productData = $this->gtmHelper->prepareProductData($product);
            $productData['quantity'] = $qty;

            $gtmData = [
                'event' => 'add_to_cart',
                'ecommerce' => [
                    'add' => [
                        'products' => [$productData]
                    ]
                ]
            ];

            $this->logger->debug('AddToCart GTM Data: ' . json_encode($gtmData));
            $this->registry->register('gtm_add_to_cart_data', $gtmData, true);
        } else {
            $this->logger->warning('AddToCart Observer: Product not found');
        }
    }
}
