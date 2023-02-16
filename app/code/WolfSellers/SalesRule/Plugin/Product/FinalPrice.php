<?php

namespace WolfSellers\SalesRule\Plugin\Product;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerModelSession;
use Magento\SalesRule\Model\RuleFactory as SalesRuleFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RulesCollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;


class FinalPrice
{
    /** @var CustomerModelSession */
    protected CustomerModelSession $customerSession;

    /** @var RulesCollectionFactory */
    protected RulesCollectionFactory $_salesRuleCollectionFactory;

    /** @var Product */
    protected Product $_itemProduct;

    /** @var ProductRepository */
    protected ProductRepository $_productRepository;

    /** @var CheckoutSession */
    private CheckoutSession $checkoutSession;

    /** @var SalesRuleFactory */
    private SalesRuleFactory $_salesRuleFactory;


    /**

     * @param CustomerModelSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param SalesRuleFactory $salesRuleFactory
     * @param RulesCollectionFactory $salesRuleCollectionFactory
     * @param Product $itemProduct
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CustomerModelSession $customerSession,
        CheckoutSession $checkoutSession,
        SalesRuleFactory $salesRuleFactory,
        RulesCollectionFactory $salesRuleCollectionFactory,
        Product $itemProduct,
        ProductRepository $productRepository
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->_salesRuleFactory = $salesRuleFactory;
        $this->_salesRuleCollectionFactory = $salesRuleCollectionFactory;
        $this->_itemProduct = $itemProduct;
        $this->_productRepository = $productRepository;
    }

    /**
     * @param Product\Type\Price $subject
     * @param $result
     * @return mixed
     */

    public function afterGetFinalPrice(
        \Magento\Catalog\Model\Product\Type\Price $subject,
                                            $result
    ) {
        try {
            $items = $this->checkoutSession->getQuote()->getItems();
            if ($items):
                foreach ($items as $item):
                    $productRule = $this->hasRuleFromProduct($item->getSku());
                    if ($productRule['validate'] == true):
                        $finalPrice = $productRule['price'];
                        $item->setPrice($finalPrice * $item->getQty() );
                    endif;
                endforeach;
            endif;
            return $result;

        } catch (\Exception $e) {
            return $result;
        }
    }

    /**
     * @return array
     */

    public function getCustomerGroupRules(): array
    {
        $result = [];
        try {
            $currentGroupId = $this->checkoutSession->getQuote()->getCustomer()->getGroupId();
            $salesRuleByCustomerGroup = $this->_salesRuleCollectionFactory->create()->addCustomerGroupFilter($currentGroupId);
            $rulesGroup = $salesRuleByCustomerGroup->getData();
            foreach ($rulesGroup as $rule):
                if($rule['is_active'] ==1 && $rule['apply_original_price']==1 && $rule['coupon_type'] !== 2):
                    $result[] = $rule['rule_id'];
                endif;
            endforeach;

            return $result;

        } catch (\Exception $e) {
            return $result;
        }
    }

    /**
     * @param $sku
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function hasRuleFromProduct($sku): array
    {
        $product = $this->_productRepository->get($sku);
        $productId = $product->getId();
        $customerRule=$this->getCustomerGroupRules();
        $_rules = $this->_salesRuleFactory->create()->getCollection();
        $result= [];
        $result['validate'] = false;
        foreach($_rules as $rule){
            if(  in_array($rule->getData('rule_id'), $customerRule) && $rule->getData('is_active') == 1 && $rule->getData('apply_original_price')== 1):
                $product = $this->_itemProduct->load($productId);
                $item = $this->_itemProduct;
                $item->setProduct($product);
                $result['validate'] = $rule->getActions()->validate($item);
                $result['price'] = $product->getPrice();
            endif;
        }

        return $result;
    }


}
