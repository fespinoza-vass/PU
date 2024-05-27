<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-05-06
 * Time: 12:08
 */

declare(strict_types=1);

namespace WolfSellers\FreeGiftBanner\Block\Product;

use Amasty\Promo\Api\Data\GiftRuleInterface;
use Amasty\Promo\Model\RuleResolver;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SalesRule\Model\Rule;

/**
 * Gift Banner.
 */
class Banner extends Template
{
    /** @var Product|null */
    private ?Product $product = null;

    /** @var Registry */
    private Registry $coreRegistry;

    /** @var RuleCollectionFactory */
    private RuleCollectionFactory $ruleCollectionFactory;

    /** @var CustomerSession */
    private CustomerSession $customerSession;

    /** @var RuleResolver */
    private RuleResolver $ruleResolver;

    /** @var ItemFactory */
    private ItemFactory $quoteItemFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param CustomerSession $customerSession
     * @param RuleResolver $ruleResolver
     * @param ItemFactory $quoteItemFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RuleCollectionFactory $ruleCollectionFactory,
        CustomerSession $customerSession,
        RuleResolver $ruleResolver,
        ItemFactory $quoteItemFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->customerSession = $customerSession;
        $this->ruleResolver = $ruleResolver;
        $this->quoteItemFactory = $quoteItemFactory;

        parent::__construct($context, $data);
    }

    /**
     * Get product.
     *
     * @return Product
     */
    public function getProduct(): Product
    {
        if (!$this->product) {
            $this->product = $this->coreRegistry->registry('product');
        }

        return $this->product;
    }

    /**
     * Free gifts.
     *
     * @return bool
     */
    public function hasFreeGift(): bool
    {
        $item = $this->getQuoteItem();
        $freeGift = false;

        /* @var $rule Rule */
        foreach ($this->getRules() as $rule) {
            $valid = $rule->getActions()->validate($item);

            if (!$valid) {
                continue;
            }

            $ampromoRule = $this->ruleResolver->getFreeGiftRule($rule);
            $promoSku = $ampromoRule->getSku();

            if (!$promoSku) {
                continue;
            }

            $freeGift = true;
            break;
        }

        return $freeGift;
    }

    /**
     * Get rules.
     *
     * @return Collection
     */
    private function getRules(): Collection
    {
        $collection = $this->ruleCollectionFactory->create();
        $collection
            ->addWebsiteGroupDateFilter(
                $this->_storeManager->getWebsite()->getId(),
                $this->customerSession->getCustomerGroupId()
            )
            ->addFieldToFilter('simple_action', GiftRuleInterface::PER_PRODUCT)
            ->addFieldToFilter('is_active', true)
        ;

        return $collection->load();
    }

    /**
     * Simulate quote item.
     *
     * @return Item
     */
    private function getQuoteItem(): Item
    {
        $item = $this->quoteItemFactory->create();
        $item->setStoreId($this->_storeManager->getStore()->getId());
        $item->setProduct($this->getProduct());
        $this->setQty(1);

        return $item;
    }
}
