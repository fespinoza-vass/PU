<?php

namespace WolfSellers\DiorMenu\Block\Product\View;
use \Amasty\ShopbyBase\Model\OptionSettingRepository;
use \Amasty\ShopbyBase\Model\OptionSetting;
class Description extends \Magento\Catalog\Block\Product\View\Description
{
    private \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository;
    private OptionSettingRepository $optionSettingRepository;
    private OptionSetting $optionSetting;

    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        OptionSettingRepository $optionSettingRepository,
        OptionSetting $optionSetting,
        \Magento\Framework\Registry $registry, array $data = []
    )
    {
        parent::__construct($context, $registry, $data);
        $this->attributeRepository = $attributeRepository;
        $this->optionSettingRepository = $optionSettingRepository;
        $this->optionSetting = $optionSetting;
    }

    public function getManufactureImage($idAttribute){
        return $this->optionSetting->getByParams("attr_manufacturer",$idAttribute,1)->getImageUrl();
    }

    public function getManufactureImageSlider($idAttribute){
        return $this->optionSetting->getByParams("attr_manufacturer",$idAttribute,1)->getSliderImageUrl();
    }
}
