<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitcart
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsplitcart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Webkul Mpsplitcart SalesQuoteRemoveItemObserver Observer
 */
class SalesQuoteRemoveItemObserver implements ObserverInterface
{
    /**
     * @var \Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     */
    public function __construct(
        \Webkul\Mpsplitcart\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * [executes when sales_quote_remove_item event hit and used to
     *  update virtual cart when any item is removed from sales quote]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $quoteItem = $observer->getQuoteItem();
            $itemId = $quoteItem->getItemId();

            $virtualCart = $this->helper->getVirtualCart();
            $removeItemCheck = $this->helper->getCheckoutRemoveSession();
            $moduleEnabledCheck = $this->helper->checkMpsplitcartStatus();

            if ($virtualCart
                && is_array($virtualCart)
                && $virtualCart !== ""
                && $moduleEnabledCheck
                && (!$removeItemCheck
                || $removeItemCheck !== 1
                || $removeItemCheck == null)
            ) {
                foreach ($virtualCart as $sellerId => $sellerArray) {
                    foreach ($sellerArray as $productId => $productData) {
                        if ($productId !== "grouped"
                            && $productData['item_id'] == $itemId
                        ) {
                            unset($virtualCart[$sellerId][$productId]);
                        } elseif ($productId == "grouped") {
                            foreach ($productData as $groupProId => $groupInner) {
                                if ($groupInner['item_id'] == $itemId
                                ) {
                                    unset($virtualCart[$sellerId]['grouped'][$groupProId]);
                                }
                            }
                        }
                    }
                    if (array_key_exists('grouped', $virtualCart[$sellerId])
                        && empty($virtualCart[$sellerId]['grouped'])
                    ) {
                        unset($virtualCart[$sellerId]['grouped']);
                    }
                    $check = $this->helper->checkEmptyVirtualCart(
                        $virtualCart[$sellerId]
                    );
                    if ($check) {
                        unset($virtualCart[$sellerId]);
                    }
                }
                $this->helper->setVirtualCart($virtualCart);
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("SalesQuoteRemoveItemObserver execute : ".$e->getMessage());
        }
    }
}
