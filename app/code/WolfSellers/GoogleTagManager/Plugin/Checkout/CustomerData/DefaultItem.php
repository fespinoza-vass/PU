<?php

namespace WolfSellers\GoogleTagManager\Plugin\Checkout\CustomerData;

class DefaultItem
{
    public function aroundGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $data = $proceed($item);

        $attributes = $item->getProduct()->getAttributes();
        $category = null;
        $subcategory = null;
        $brand = null;
        $gender = null;
        $size = null;
        foreach($attributes as $attribute){
            if($attribute->getName() === 'categoria') {
                $category = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'sub_categoria') {
                $subcategory = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'manufacturer') {
                $brand = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'genero') {
                $gender = $attribute->getFrontend()->getValue($item->getProduct());
            }
            if($attribute->getName() === 'tamano') {
                $size = $attribute->getFrontend()->getValue($item->getProduct());
                if( !$size ) $size = null;
            }
        }

        $result['category'] = $category;
        $result['subcategory'] = $subcategory;
        $result['brand'] = $brand;
        $result['gender'] = $gender;
        $result['size'] = $size;
        return \array_merge(
            $result,
            $data
        );
    }
}
