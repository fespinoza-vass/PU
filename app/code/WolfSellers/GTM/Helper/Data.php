<?php
namespace WolfSellers\GTM\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function prepareProductData($product)
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getFinalPrice(),
            'sku' => $product->getSku(),
            'category' => $product->getCategory() ? $product->getCategory()->getName() : '',
        ];
    }
}
