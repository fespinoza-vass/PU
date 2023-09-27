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
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session\Proxy $sessionProxy,
        \Magento\CatalogRule\Model\ResourceModel\Rule $rule,
        \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $catalogRuleRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->_date =  $date;
        $this->_storeManager = $storeManager;
        $this->sessionProxy= $sessionProxy;
        $this->ruleResource = $rule;
        $this->catalogRuleRepository = $catalogRuleRepository;
        $this->_categoryRepository = $categoryRepository;
        $this->_productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Checkout\CustomerData\AbstractItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    public function aroundGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $data = $proceed($item);

        $attributes = $item->getProduct()->getAttributes();
        $category = null;
        $subcategory = null;
        $family = null;
        $brand = null;
        $gender = null;
        $size = null;
        foreach($attributes as $attribute){
            if($attribute->getName() === 'categoria') {
                $category = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'sub_categoria') {
                $subcategory = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'familia') {
                $family = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'manufacturer') {
                $brand = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'genero') {
                $gender = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'tamano') {
                $size = $attribute->getFrontend()->getValue($item->getProduct());
                if( !$size ) $size = null;
            }
        }

        $product = $this->getProductById($data['product_id']);

        /** Get Rules of product */
        $rules = $this->getRules($product->getId());
        $dataRule = [];
        if($rules){
            foreach ($rules as $rule){
                $dataRule[] = $rule;
            }
        }
        $dataRule = implode( ', ', $dataRule);

        /** Get Name Categories of product */
        /* $categories = $this->getCategoryName($product);
        $category = isset($categories[0]) ? $categories[0] : '';
        $subcategory = isset($categories[1]) ? $categories[1] : '';
        $family = isset($categories[2]) ? $categories[2] : '';*/

        $result['category'] = $category;
        $result['subcategory'] = $subcategory;
        $result['familia'] = $family;
        $result['brand'] = $brand;
        $result['gender'] = $gender;
        $result['size'] = $size;
        $result['promotion'] = $dataRule;

        return \array_merge(
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
    public function getCategoryName($product){
        $categories = [];
        $this->logger->debug('CATEGORYIDS: ');
        $this->logger->debug(print_r($product->getCategoryIds(), true));
        foreach($product->getCategoryIds() as $categoryId){
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

        $this->logger->debug('DATE: '.$date);
        $this->logger->debug('WEBSITEID: '.$websiteId);
        $this->logger->debug('CUSTOMERGROUPID: '.$customerGroupId);

        $rules = $this->ruleResource->getRulesFromProduct($date, $websiteId, $customerGroupId, $productId);
        $promos = [];

        foreach ($rules as $rule){
            $promo = $this->catalogRuleRepository->get($rule['rule_id']);
            array_push($promos, $promo->getName());
        }

        return $promos;
    }
}
