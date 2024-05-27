<?php

namespace WolfSellers\Bopis\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;

class Bopis extends Generic
{
    public function isVisible(){
        $product = $this->getItem();
        return strpos($this->getRequest()->getModuleName(), "checkout") !== false;
    }

    public function getUpdateUrl(){
        return $this->getItem()->getProduct()->getProductUrl();
    }
}
