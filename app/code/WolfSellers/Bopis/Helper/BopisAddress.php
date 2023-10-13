<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class BopisAddress extends AbstractHelper
{
    const DELIVERY_TITLE = "carriers/tablerate/title";

    private Session $checkoutSession;
    private BopisRepositoryInterface $bopisRepository;

    public function __construct(
        Context $context,
        BopisRepositoryInterface $bopisRepository,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->bopisRepository = $bopisRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function fillAddress(&$jsLayout): void{
        try{
            $quoteId = $this->checkoutSession->getQuoteId();
            $bopis = $this->bopisRepository->getByQuoteId($quoteId);
        }catch (\Exception $exception){
            return;
        }

        $addressData = json_decode($bopis->getAddressObject(), true);

        if ((isset($addressData['address_id']) AND $addressData['address_id'] != null)) return;
        $fields = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        if ($bopis->getType() == 'store-pickup'){
            $this->fillStoreAddress($jsLayout, $fields, $addressData);
            return;
        }

        if ($bopis->getType() == 'delivery') return;

        foreach ($fields as $name => &$field) {
            if (isset($addressData[$name])){
                $value = $addressData[$name];
                $field['value'] = $value;
            }
            if ($name === "street"){
                foreach ($field['children'] as $i => &$child) {
                    if (isset($addressData[$name . "_" . ++$i])){
                        $value = $addressData[$name . "_" . $i];
                        $child['value'] = $value;
                    }
                }
            }
            if ($name === "identificacion"){
                $value = $addressData[$name];
                $field['value'] = (int) $value;
            }
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = $fields;

    }

    private function fillStoreAddress(&$jsLayout, $fields, $addressData){
        $prefix = strtolower($addressData['country_id']);
        if ($prefix == "CO"){
            $prefix = "co";
        }
        if ($prefix == "CR"){
            $prefix = "cr";
        }
        foreach ($fields as $name => &$field) {
            switch ($name){
                case "street":
                    if ($prefix == "co"){
                        foreach ($field['children'] as $i => &$child) {
                            if ($i == 0){
                                $child['value'] = $addressData['street'];
                            }else{
                                $child['value'] = $addressData[$prefix . '_street' . ++$i];
                            }
                        }
                    }else{
                        foreach ($field['children'] as $i => &$child) {
                            if ($i == 0){
                                $child['value'] = $addressData['street'];
                            }
                        }
                    }
                    break;
                case "tipo_direccion":
                    if ($prefix == "co") $field['value'] = $addressData[$prefix . '_tipo_direccion'];
                    break;
                case "informacion_adicional":
                    if ($prefix == "co") $field['value'] = $addressData[$prefix . '_additional_info'];
                    break;
                case "country_id":
                    $field['value'] = $addressData['country_id'];
                    break;
                case "region_id":
                    $field['value'] = $addressData['region_id'];
                    break;
                case "region":
                    $field['value'] = $addressData['region'];
                    break;
                case "city":
                    $field['value'] = $addressData['city'];
                    break;
                case "postcode":
                    $field['value'] = $addressData['postcode'];
                    break;
                case "province_id":
                    if ($prefix != "cr"){
                        $field['value'] = $addressData[$prefix . '_ciudad'];
                    }elseif($prefix == "cr"){
                        $field['value'] = $addressData[$prefix . '_canton'];
                    }
                    break;
                case "district_id":
                    if ($prefix != "co"){
                        $field['value'] = $addressData[$prefix . '_distrito'];
                    }
                    break;
            }
        }
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = $fields;
    }

    public function getBopisAddress($quoteId){
        try{
            return $this->bopisRepository->getByQuoteId($quoteId);
        }catch (\Exception $exception){
            return false;
        }
    }

    public function getTypeCopy($type){
        $title = $this->scopeConfig->getValue(self::DELIVERY_TITLE);
        return $type == 'delivery' ? "<strong>" . $title . "</strong>" : "<strong>" . __("Recoger en tienda (Gratis)") . "</strong><span>" . __("(Pick up after 4 business hours)") . "</span>";
    }


    /**
     * Get coppy for email
     *
     * @param $type
     * @return string
     */
    public function getTypeCopyEmail($type){
        $title = $this->scopeConfig->getValue(self::DELIVERY_TITLE);
        return $type == 'delivery' ? "<strong>" . $title . "</strong>" : "<strong>" . __("Recoger en tienda") . "</strong>";
    }

    public function getDeliveryAddress($bopis){
        $obj = json_decode($bopis->getAddressObject(), true);
        return $bopis->getType() == 'delivery' ? $bopis->getAddressFormatted() : $obj['store_street'];
    }
}
