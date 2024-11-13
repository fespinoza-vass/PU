<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_GTM
 * @author VASS Team
 */

namespace WolfSellers\GTM\Observer;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Checkout\Model\Session as CheckoutSession;
use WolfSellers\GTM\Block\Ga as GaBlock;

class BeginCheckout implements ObserverInterface
{
    /**
     * @param Registry $registry
     * @param CheckoutSession $checkoutSession
     * @param GaBlock $gaBlock
     * @param ProductRepository $productRepository
     * @param Session $customerSession
     */
    public function __construct(
        private Registry $registry,
        private CheckoutSession $checkoutSession,
        private GaBlock $gaBlock,
        private ProductRepository $productRepository,
        private Session $customerSession
    ) {
    }

    public function execute(Observer $observer)
    {
        $quote = $this->checkoutSession->getQuote();

        if ($quote && $quote->getId()) {
            $products = [];
            $productsIds = [];
            $productsSkus = [];
            $totalPrice = 0;
            $totalQty = 0;

            foreach ($quote->getAllVisibleItems() as $item) {

                $product = $this->productRepository->getById($item->getProductId());
                $imageUrl = $this->gaBlock->imageHelper->init($item, 'product_base_image')->getUrl();
                $categories = [];
                if ($product->getCategoryIds()) {
                    foreach ($product->getCategoryIds() as $categoryId) {
                        try {
                            $categoryObj = $this->gaBlock->_categoryRepository->get($categoryId);
                            $categories[] = $categoryObj->getName();
                        } catch (Exception $e) {
                            throw $e;
                        }
                    }
                }

                $category = $product->getData('categoria') ?? '';
                $subcategory = $product->getData('sub_categoria') ?? '';
                $family = $product->getData('familia') ?? '';
                $brand = $product->getAttributeText('manufacturer') ?? '';
                $gender = $product->getAttributeText('genero') ?? '';
                $size = $product->getAttributeText('tamano') ?? '';
                $price = number_format($item->getProduct()->getFinalPrice(), 2);

                $productData = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'price' => $price,
                    'category' => $category,
                    'sub_categoria' => $subcategory,
                    'familia' => $family,
                    'genero' => $gender,
                    'tamano' => $size,
                    'quantity' => $item->getQty(),
                    'promotion' => $this->gaBlock->getRules($product->getId()),
                    'brand' => $brand,
                    'productURL' => $product->getProductUrl(),
                    'imageURL' => $imageUrl
                ];

                $productPrice = round($item->getProduct()->getFinalPrice(), 2);
                $products[] = $productData;
                $productsIds[] = $item->getProduct()->getId();
                $productsSkus[] = $item->getProduct()->getSku();
                $totalPrice += $productPrice * $item->getQty();
                $totalQty += $item->getQty();
            }

            $customer = $this->customerSession->getCustomer();
            if ($customer?->getId()) {
                $dataUser = [
                    'email' => $customer->getEmail(),
                    'first_name' => $customer->getFirstname(),
                    'last_name' => $customer->getLastname(),
                ];
            } else {
                $dataUser = [];
            }


            $gtmData = [
                'event' => 'begin_checkout',
                'pagePostAuthor' => "Perfumerias Unidas",
                'ecomm_pagetype' => "Checkout",
                'ecomm_prodid' => $productsIds,
                'ecomm_prodsku' => $productsSkus,
                'ecomm_totalvalue' => (float)number_format($totalPrice, 2),
                'ecomm_totalquantity' => (int)$totalQty,
                'ecommerce' => [
                    'checkout' => [
                        'actionField' => ['step' => 1],
                        'products' => $products
                    ]
                ],
                'dataUser' => $dataUser
            ];

            $this->registry->register('gtm_begin_checkout_data', $gtmData, true);
        }
    }

    /**
     * Obtener el label del atributo personalizado
     */
    private function getCustomAttributeLabel($product, $attributeCode)
    {
        $attributeValue = $product->getData($attributeCode);
        if ($attributeValue) {
            $options = $this->gaBlock->attributerepository->get($attributeCode)->getOptions();
            foreach ($options as $option) {
                if ($option->getValue() == $attributeValue) {
                    return $option->getLabel();
                }
            }
        }
        return '';
    }
}
