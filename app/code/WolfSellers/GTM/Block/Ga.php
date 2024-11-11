<?php

namespace WolfSellers\GTM\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\Cookie\Helper\Cookie;
use Magento\Customer\Model\Session\Proxy;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\GoogleTagManager\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Google Analytics Block
 *
 * @api
 * @since 100.0.2
 */
class Ga extends \Magento\GoogleAnalytics\Block\Ga
{
    /**
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $helper;

    /**
     * @var Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    private \Magento\Catalog\Helper\Data $_catalogHelper;
    public Image $imageHelper;
    private CatalogRuleRepositoryInterface $catalogRuleRepository;
    private TimezoneInterface $_date;
    private Proxy $sessionProxy;
    private Rule $ruleResource;
    public CategoryRepositoryInterface $_categoryRepository;
    public Repository $attributerepository;

    /**
     * @param Context $context
     * @param CollectionFactory $salesOrderCollection
     * @param Data $googleAnalyticsData
     * @param Cookie $cookieHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Repository $attributerepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CatalogRuleRepositoryInterface $catalogRuleRepository
     * @param Rule $rule
     * @param StoreManagerInterface $storeManager
     * @param Proxy $sessionProxy
     * @param TimezoneInterface $date
     * @param Image $imageHelper
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param array $data
     */
    public function __construct(
        Context                             $context,
        CollectionFactory                   $salesOrderCollection,
        Data                                $googleAnalyticsData,
        Cookie                              $cookieHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Repository                          $attributerepository,
        CategoryRepositoryInterface         $categoryRepository,
        CatalogRuleRepositoryInterface      $catalogRuleRepository,
        Rule                                $rule,
        StoreManagerInterface               $storeManager,
        Proxy                               $sessionProxy,
        TimezoneInterface                   $date,
        Image                               $imageHelper,
        \Magento\Catalog\Helper\Data        $catalogHelper,
        array                               $data = []
    )
    {
        $this->cookieHelper = $cookieHelper;
        $this->jsonHelper = $jsonHelper;
        $this->attributerepository = $attributerepository;
        $this->_categoryRepository = $categoryRepository;
        $this->ruleResource = $rule;
        $this->_storeManager = $storeManager;
        $this->sessionProxy = $sessionProxy;
        $this->_date = $date;
        $this->catalogRuleRepository = $catalogRuleRepository;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $salesOrderCollection, $googleAnalyticsData, $data, $cookieHelper);
        $this->_catalogHelper = $catalogHelper;
    }

    /**
     * Is gtm available
     *
     * @return bool
     */
    protected function _isAvailable()
    {
        return $this->_googleAnalyticsData->isGoogleAnalyticsAvailable();
    }

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get store currency code for page tracking javascript code
     *
     * @return string
     */
    public function getStoreCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Render information about specified orders and their items
     *
     * @return string
     * @deprecated 100.2.0 please use getOrdersDataArray method
     */
    public function getOrdersData()
    {
        $result = [];
        foreach ($this->getOrdersDataArray() as $orderDara) {
            $result[] = 'dataLayer.push(' . $this->jsonHelper->jsonEncode($orderDara) . ");\n";
        }
        return implode("\n", $result);
    }

    /**
     * Check if user not allow to save cookie
     *
     * @return bool
     */
    public function isUserNotAllowSaveCookie()
    {
        return $this->cookieHelper->isUserNotAllowSaveCookie();
    }

    /*
     * Function to obtain product promotion
     */
    public function getRules($productId)
    {
        $date = $this->_date->date()->format('Y-m-d H:i:s');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->sessionProxy->getCustomer()->getGroupId();

        $rules = $this->ruleResource->getRulesFromProduct($date, $websiteId, $customerGroupId, $productId);
        $promos = [];

        foreach ($rules as $rule) {
            $promo = $this->catalogRuleRepository->get($rule['rule_id']);
            array_push($promos, $promo->getName());
        }

        return $promos;
    }

    /**
     * Retries current product to send via gtag
     * @return Product|null
     */
    public function getProductBlock(): ?Product
    {
        return $this->_catalogHelper->getProduct();
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCategoryName(): array
    {

        $product = $this->getProductBlock();
        $categories = [];

        if (empty($product)) {

            return $categories;
        }

        foreach ($product->getCategoryIds() as $categoryId) {

            $categories[] = $this->_categoryRepository->get($categoryId)->getName();
        }
        return $categories;
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getCurrencyCode(): ?string
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }
}
