<?php
/*
 * /**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_GTM
 * @author VASS Team
*/

/* The data is added:
 * category
 * subcategory
 * brand
 * gender
 * size
 * promotion
 *
 * In the CustomerData of the product, to be able to send them to the Tag Manager
 */

namespace WolfSellers\GTM\Plugin\Checkout\CustomerData;

use Closure;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Customer\Model\Session\Proxy;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use function array_merge;

class DefaultItem
{
    /*
     * @param TimezoneInterface $date
     * @param StoreManagerInterface $storeManager
     * @param Proxy $sessionProxy
     * @param Rule $rule
     * @param CatalogRuleRepositoryInterface $catalogRuleRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepository $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        TimezoneInterface    $date,
        StoreManagerInterface              $storeManager,
        Proxy                   $sessionProxy,
        Rule           $rule,
        CatalogRuleRepositoryInterface $catalogRuleRepository,
        CategoryRepositoryInterface        $categoryRepository,
        ProductRepository                $productRepository,
        LoggerInterface                                $logger)
    {
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->sessionProxy = $sessionProxy;
        $this->ruleResource = $rule;
        $this->catalogRuleRepository = $catalogRuleRepository;
        $this->_categoryRepository = $categoryRepository;
        $this->_productRepository = $productRepository;
        $this->logger = $logger;
    }

    public function aroundGetItemData(
        AbstractItem $subject,
        Closure                                    $proceed,
        Item             $item
    )
    {
        $data = $proceed($item);
        $product = $this->getProductById($data['product_id']);

        /** Get Rules of product */
        $rules = $this->getRules($product->getId());
        $dataRule = [];
        if ($rules) {
            foreach ($rules as $rule) {
                $dataRule[] = $rule;
            }
        }
        $dataRule = implode(', ', $dataRule);

        $category = $product->getData('categoria') ?? '';
        $subcategory = $product->getData('sub_categoria') ?? '';
        $family = $product->getData('familia') ?? '';
        $brand = $product->getAttributeText('manufacturer') ?? '';
        $gender = $product->getAttributeText('genero') ?? '';
        $size = $product->getAttributeText('tamano') ?? '';

        $result['category'] = $category;
        $result['subcategory'] = $subcategory;
        $result['familia'] = $family;
        $result['brand'] = $brand;
        $result['gender'] = $gender;
        $result['size'] = $size;
        $result['promotion'] = $dataRule;

        return array_merge(
            $result,
            $data
        );
    }

    /** Function get product by ID */
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    /** Function get product by SKU */
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    /*
     * Function for get Name Category
     */
    public function getCategoryName($product)
    {
        $categories = [];
        $this->logger->debug('CATEGORYIDS: ');
        $this->logger->debug(print_r($product->getCategoryIds(), true));
        foreach ($product->getCategoryIds() as $categoryId) {
            array_push($categories, $this->_categoryRepository->get($categoryId)->getName());
        }
        return $categories;
    }

    /*
     * Function for get Rule of product
     */
    public function getRules($productId)
    {
        $date = $this->_date->date()->format('Y-m-d H:i:s');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->sessionProxy->getCustomer()->getGroupId();

        $this->logger->debug('DATE: ' . $date);
        $this->logger->debug('WEBSITEID: ' . $websiteId);
        $this->logger->debug('CUSTOMERGROUPID: ' . $customerGroupId);

        $rules = $this->ruleResource->getRulesFromProduct($date, $websiteId, $customerGroupId, $productId);
        $promos = [];

        foreach ($rules as $rule) {
            $promo = $this->catalogRuleRepository->get($rule['rule_id']);
            array_push($promos, $promo->getName());
        }

        return $promos;
    }
}
