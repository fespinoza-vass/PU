<?php
/*
 * The data is added:
 * category
 * subcategory
 * brand
 * gender
 * size
 * promotion
 *
 * In the CustomerData of the product, to be able to send them to the Tag Manager
 */

namespace WolfSellers\GoogleTagManager\Plugin\Checkout\CustomerData;

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
    public function __construct(TimezoneInterface $date, StoreManagerInterface $storeManager, Proxy $sessionProxy, Rule $rule, CatalogRuleRepositoryInterface $catalogRuleRepository, CategoryRepositoryInterface $categoryRepository, ProductRepository $productRepository, LoggerInterface $logger)
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

    public function aroundGetItemData(AbstractItem $subject, Closure $proceed, Item $item)
    {
        $data = $proceed($item);

        $product = $this->_productRepository->getById($item->getProductId());

        $category = $product->getData('categoria') ?? '';
        $subcategory = $product->getData('sub_categoria') ?? '';
        $brand = $product->getAttributeText('manufacturer') ?? '';
        $gender = $product->getAttributeText('genero') ?? '';
        $size = $product->getData('tamano') ?? '';
        $family = $product->getData('familia') ?? '';

        /** Get Rules of product */
        $rules = $this->getRules($product->getId());
        $dataRule = $rules ? implode(', ', $rules) : '';

        $result = ['category' => $category, 'subcategory' => $subcategory, 'familia' => $family, 'brand' => $brand, 'gender' => $gender, 'size' => $size, 'promotion' => $dataRule];

        return array_merge($result, $data);
    }

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

    /** Function get product by ID */
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    /*
     * Function for get Name Category
     */

    /** Function get product by SKU */
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    /*
     * Function for get Rule of product
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
}
