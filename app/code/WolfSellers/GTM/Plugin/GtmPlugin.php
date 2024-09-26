<?php

namespace WolfSellers\GTM\Plugin;

use Magento\GoogleTagManager\Block\Ga;

class GtmPlugin
{
    public function afterGetOrdersTrackingData(Ga $subject, $result)
    {
        if (is_array($result) && isset($result['ecommerce']['purchase'])) {
            unset($result['ecommerce']['purchase']);
            if (empty($result['ecommerce'])) {
                unset($result['ecommerce']);
            }
            if (empty($result)) {
                return null;
            }
        }
        return $result;
    }

    public function afterGetAjaxAddToCartData(Ga $subject, $result)
    {
        if (is_array($result) && isset($result['event']) && $result['event'] === 'addToCart') {
            return null; // Devolvemos null para eliminar completamente el evento
        }
        return $result;
    }

    public function afterGetProductAddToCartData(Ga $subject, $result)
    {
        if (is_array($result) && isset($result['event']) && $result['event'] === 'addToCart') {
            return null; // Devolvemos null para eliminar completamente el evento
        }
        return $result;
    }
}
