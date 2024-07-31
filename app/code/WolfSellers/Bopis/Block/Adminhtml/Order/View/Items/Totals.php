<?php


namespace WolfSellers\Bopis\Block\Adminhtml\Order\View\Items;


use Magento\Quote\Model\Quote\Item\AbstractItem;

class Totals extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    private \Magento\Checkout\Helper\Data $checkoutHelper;

    /**
     * Totals constructor.
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        array $data = []
    ){
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
        $this->checkoutHelper = $checkoutHelper;
    }

    public function getSubtotal($item) {
        $this->checkoutHelper->getBaseSubtotalInclTax($item);
    }

    public function createPriceBlock($item) {
        return $this->getLayout()
            ->createBlock('Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn')
            ->setTemplate('Magento_Sales::items/price/row.phtml')
            ->setItem($item)
            ->toHtml();
    }
}
