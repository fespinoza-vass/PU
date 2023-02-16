<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2023-01-10
 * Time: 15:46
 */

declare(strict_types=1);

namespace WolfSellers\SalesRule\Observer;

use Magento\CatalogRule\Model\ResourceModel\RuleFactory as ResourceRuleFactory;
use Magento\CatalogRule\Observer\RulePricesStorage;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerModelSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\ResourceModel\RuleFactory as ResourceSalesRuleFactory;
use Magento\SalesRule\Model\RuleFactory as SalesRuleFactory;
use Magento\Store\Model\StoreManagerInterface;


/**
 * Observer for applying catalog rules on product for frontend area.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class ProcessFrontFinalPriceObserver implements ObserverInterface
{
    /** @var CustomerModelSession */
    protected CustomerModelSession $customerSession;

    /** @var StoreManagerInterface */
    protected StoreManagerInterface $storeManager;

    /** @var TimezoneInterface */
    protected TimezoneInterface $localeDate;

    /** @var ResourceRuleFactory */
    protected ResourceRuleFactory $resourceRuleFactory;

    /** @var RulePricesStorage */
    protected RulePricesStorage $rulePricesStorage;


    /** @var CheckoutSession */
    private CheckoutSession $checkoutSession;

    /** @var CouponFactory */
    private CouponFactory $couponFactory;

    /** @var SalesRuleFactory */
    private SalesRuleFactory $salesRuleFactory;

    /** @var ResourceSalesRuleFactory */
    private ResourceSalesRuleFactory $_resourceSalesRuleFactory;

    /**
     * @param RulePricesStorage $rulePricesStorage
     * @param ResourceRuleFactory $resourceRuleFactory
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param CustomerModelSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param CouponFactory $couponFactory
     * @param SalesRuleFactory $salesRuleFactory
     * @param ResourceSalesRuleFactory $resourceSalesRuleFactory
     */
    public function __construct(
        RulePricesStorage $rulePricesStorage,
        ResourceRuleFactory $resourceRuleFactory,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        CustomerModelSession $customerSession,
        CheckoutSession $checkoutSession,
        CouponFactory $couponFactory,
        SalesRuleFactory $salesRuleFactory
    ) {
        $this->rulePricesStorage = $rulePricesStorage;
        $this->resourceRuleFactory = $resourceRuleFactory;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->couponFactory = $couponFactory;
        $this->salesRuleFactory = $salesRuleFactory;
    }

    /**
     * Apply catalog price rules to product on frontend.
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $pId = $product->getId();
        $storeId = $product->getStoreId();

        if ($observer->hasDate()) {
            $date = new \DateTime($observer->getEvent()->getDate());
        } else {
            $date = $this->localeDate->scopeDate($storeId);
        }

        if ($observer->hasWebsiteId()) {
            $wId = $observer->getEvent()->getWebsiteId();
        } else {
            $wId = $this->storeManager->getStore($storeId)->getWebsiteId();
        }

        if ($observer->hasCustomerGroupId()) {
            $gId = $observer->getEvent()->getCustomerGroupId();
        } elseif ($product->hasCustomerGroupId()) {
            $gId = $product->getCustomerGroupId();
        } else {
            $gId = $this->customerSession->getCustomerGroupId();
        }

        $key = "{$date->format('Y-m-d H:i:s')}|{$wId}|{$gId}|{$pId}";
        if (!$this->rulePricesStorage->hasRulePrice($key)) {
            $rulePrice = $this->resourceRuleFactory->create()->getRulePrice($date, $wId, $gId, $pId);
            $this->rulePricesStorage->setRulePrice($key, $rulePrice);
        }

        if (false !== $this->rulePricesStorage->getRulePrice($key)) {
            $finalPrice = min($product->getData('final_price'), $this->rulePricesStorage->getRulePrice($key));

            if ($this->hasCouponOriginalPrice()) {
                $finalPrice = max($product->getData('final_price'), $this->rulePricesStorage->getRulePrice($key));
            }

            $product->setFinalPrice($finalPrice);
        }

        return $this;
    }

    /**
     * Has coupon original price.
     *
     * @return bool
     */
    private function hasCouponOriginalPrice(): bool
    {
        try {
            $couponCode = $this->checkoutSession->getQuote()->getCouponCode();

            if (empty($couponCode)) {
                return false;
            }

            $ruleId = $this->couponFactory->create()->loadByCode($couponCode)->getRuleId();
            $salesRule = $this->salesRuleFactory->create();
            $this->_resourceSalesRuleFactory->create()->load($salesRule, $ruleId);

            return (bool) $salesRule->getData('apply_original_price');
        } catch (\Exception $e) {
            return false;
        }
    }

}
