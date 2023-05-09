<?php

namespace WolfSellers\DiorMenu\Block\Product\View;

use Amasty\ShopbyBase\Model\OptionSetting;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class Description extends \Magento\Catalog\Block\Product\View\Description
{
    /**
     * @var OptionSetting
     */
    private OptionSetting $optionSetting;

    /**
     * @param Context $context
     * @param OptionSetting $optionSetting
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context       $context,
        OptionSetting $optionSetting,
        Registry      $registry,
        array         $data = []
    )
    {
        parent::__construct($context, $registry, $data);

        $this->optionSetting = $optionSetting;
    }

    /**
     * @param $idAttribute
     * @return string|null
     */
    public function getManufactureImage($idAttribute): ?string
    {
        $idAttribute = (int) $idAttribute;

        return $this->optionSetting->getByParams("attr_manufacturer", $idAttribute, 1)->getImageUrl();
    }

    /**
     * @param $idAttribute
     * @return string|null
     */
    public function getManufactureUrlAlias($idAttribute): ?string
    {
        $idAttribute = (int) $idAttribute;

        return $this->optionSetting->getByParams("attr_manufacturer", $idAttribute, 1)->getUrlAlias();
    }
}
