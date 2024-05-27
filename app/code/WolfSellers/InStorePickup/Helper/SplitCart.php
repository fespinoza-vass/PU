<?php
namespace WolfSellers\InStorePickup\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;

/**
 *
 */
class SplitCart
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var DynamicTagRules
     */
    private $dynamicTagRules;

    /**
     * @param CheckoutSession $checkoutSession
     * @param DynamicTagRules $dynamicTagRules
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        DynamicTagRules $dynamicTagRules
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->dynamicTagRules = $dynamicTagRules;
    }

    /**
     * @return bool
     */
    public function isSplitCart(): bool
    {
        try {
            $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
            $hasProductWithoutLabel = false;
            $hasProductWithLabel = false;

            foreach ($items as $item) {
                $sku = $item->getProduct()->getSku();
                $labels = $this->dynamicTagRules->shippingLabelsByProductSku($sku);

                // Verifica si el producto tiene alguna etiqueta 'true'
                if ($labels['fast'] || $labels['instore']) {
                    $hasProductWithLabel = true;
                }

                // Verifica si el producto no tiene ninguna etiqueta 'true'
                if (!$labels['fast'] && !$labels['instore']) {
                    $hasProductWithoutLabel = true;
                }

                // Si encontramos productos tanto con etiquetas como sin ellas
                if ($hasProductWithLabel && $hasProductWithoutLabel) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }


}
