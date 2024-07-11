<?php


namespace WolfSellers\SkinCare\Block\Widget;


use Magento\CatalogWidget\Block\Product\ProductsList;

class ProductList extends ProductsList
{


    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        return $this;
    }
}
