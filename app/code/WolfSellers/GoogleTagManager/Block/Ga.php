<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WolfSellers\GoogleTagManager\Block;

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
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
     * @param \Magento\GoogleTagManager\Helper\Data $googleAnalyticsData
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        \Magento\GoogleTagManager\Helper\Data $googleAnalyticsData,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributerepository,
        array $data = []
    ) {
        $this->cookieHelper = $cookieHelper;
        $this->jsonHelper = $jsonHelper;
        $this->attributerepository = $attributerepository;
        parent::__construct($context, $salesOrderCollection, $googleAnalyticsData, $data, $cookieHelper);
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
     * Return information about order and items
     *
     * @return array
     * @since 100.2.0
     */
    public function getOrdersDataArray()
    {
        $result = [];
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return $result;
        }
        $collection = $this->_salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $order) {
            $orderData = [
                'id' => $order->getIncrementId(),
                'revenue' => $order->getBaseGrandTotal(),
                'tax' => $order->getBaseTaxAmount(),
                'shipping' => $order->getBaseShippingAmount(),
                'coupon' => (string)$order->getCouponCode()
            ];

            $products = [];
            /** @var \Magento\Sales\Model\Order\Item $item*/
            foreach ($order->getAllVisibleItems() as $item) {

                $category = !empty($item->getProduct()->getData('categoria')) ? $item->getProduct()->getData('categoria') : '';
                $subcategory = !empty($item->getProduct()->getData('sub_categoria')) ? $item->getProduct()->getData('sub_categoria') : '';
                //$brand = !empty($item->getProduct()->getAttributeText('brand_ids')) ? $item->getProduct()->getAttributeText('brand_ids') : '';
                
                $options = $this->attributerepository->get('manufacturer')->getOptions();
                
                foreach($options as $options_value){
                    if($options_value->getValue() == $item->getProduct()->getData('manufacturer')){
                        $brand = $options_value->getLabel();
                    }
                }
                
                $gender = !empty($item->getProduct()->getAttributeText('genero')) ? $item->getProduct()->getAttributeText('genero') : '';
                $size = !empty($item->getProduct()->getAttributeText('tamano')) ? $item->getProduct()->getAttributeText('tamano') : '';

                $products[] = [
                    'id' => $item->getSku(),
                    'name' => $item->getName(),
                    'price' => $item->getBasePrice(),
                    'quantity' => $item->getQtyOrdered(),
                    'category'  => $category,
                    'sub_categoria' => $subcategory,
                    'brand'     => $brand,
                    'genero'    => $gender,
                    'tamano'    => $size
                ];
            }

            $result[] = [
                'ecommerce' => [
                    'purchase' => [
                        'actionField' => $orderData,
                        'products' => $products
                    ],
                    'currencyCode' => $this->getStoreCurrencyCode()
                ],
                'event' => 'purchase'
            ];
        }
        return $result;
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
}
