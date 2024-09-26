<?php

namespace WolfSellers\GTM\Observer;

use Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Checkout\Model\Session as CheckoutSession;
use WolfSellers\GTM\Block\Ga as GaBlock;
use Psr\Log\LoggerInterface;

class BeginCheckout implements ObserverInterface
{
    protected $registry;
    protected $checkoutSession;
    protected $gaBlock;
    protected $logger;

    public function __construct(
        Registry        $registry,
        CheckoutSession $checkoutSession,
        GaBlock         $gaBlock,
        LoggerInterface $logger
    )
    {
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->gaBlock = $gaBlock;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->debug('BeginCheckout Observer executed');
        $quote = $this->checkoutSession->getQuote();

        if ($quote && $quote->getId()) {
            $products = [];
            foreach ($quote->getAllVisibleItems() as $item) {

                $product = $item->getProduct();
                $this->logger->debug('Processing product ID: ' . $product->getId());
                $imageUrl = $this->gaBlock->imageHelper->init($item, 'product_base_image')->getUrl();
                $category = '';
                $subcategory = '';
                $family = '';
                $brand = '';

                $categories = [];
                if ($product->getCategoryIds()) {
                    foreach ($product->getCategoryIds() as $categoryId) {
                        try {
                            $categoryObj = $this->gaBlock->_categoryRepository->get($categoryId);
                            $categories[] = $categoryObj->getName();
                        } catch (Exception $e) {
                            $this->logger->warning('Category not found for ID: ' . $categoryId);
                        }
                    }
                }

                $this->logger->debug('Categories: ' . implode(', ', $categories));

                // Asignar valores de categorías
                if (isset($categories[0])) $category = $categories[0];
                if (isset($categories[1])) $subcategory = $categories[1];
                if (isset($categories[2])) $family = $categories[2];

                $manufacturer = $product->getData('manufacturer');
                if ($manufacturer) {
                    $options = $this->gaBlock->attributerepository->get('manufacturer')->getOptions();
                    foreach ($options as $option) {
                        if ($option->getValue() == $manufacturer) {
                            $brand = $option->getLabel();
                            break;
                        }
                    }
                }

                $this->logger->debug('Brand: ' . $brand);

                $productData = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'price' => $item->getBasePrice(),
                    'category' => $category,
                    'sub_categoria' => $subcategory,
                    'familia' => $family,
                    'genero' => $product->getAttributeText('genero') ?: '',
                    'tamano' => $product->getAttributeText('tamano') ?: '',
                    'quantity' => $item->getQty(),
                    'promotion' => $this->gaBlock->getRules($product->getId()),
                    'brand' => $brand,
                    'productURL' => $product->getProductUrl(),
                    'imageURL' => $imageUrl
                ];

                $products[] = $productData;
            }

            $gtmData = [
                'event' => 'begin_checkout',
                'ecommerce' => [
                    'checkout' => [
                        'actionField' => ['step' => 1],
                        'products' => $products
                    ]
                ],
                'checkout_total' => $quote->getGrandTotal()
            ];

            $this->logger->debug('BeginCheckout GTM Data: ' . json_encode($gtmData));
            $this->registry->register('gtm_begin_checkout_data', $gtmData, true);
        } else {
            $this->logger->warning('BeginCheckout Observer: Quote not found or empty');
        }
    }
}
