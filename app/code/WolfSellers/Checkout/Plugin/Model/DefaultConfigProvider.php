<?php
namespace WolfSellers\Checkout\Plugin\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;
use Magento\Checkout\Model\Session as CheckoutSesion;

class DefaultConfigProvider
{
    /** @var ScopeConfigInterface */
    private ScopeConfigInterface $scopeConfig;

    /** @var StoreManagerInterface */
    private StoreManagerInterface $storeManager;

    /**
     * @var CheckoutSesion
     */
    protected CheckoutSesion $checkoutSession;

    /**
     * @var DynamicTagRules
     */
    protected DynamicTagRules $dynamicTagRules;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CheckoutSesion $checkoutSession,
        DynamicTagRules $dynamicTagRules
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->dynamicTagRules = $dynamicTagRules;
    }

    /**
     * Return admin configuration.
     *
     * @param $path
     * @return mixed
     */
    public function getConfigData($path): mixed
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * After get plugin, for add var to checkout config javascript var.
     *
     * @param DefaultConfigProvider $subject
     * @param array $result
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetConfig($subject, array $result)
    {
        $mediaUrl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $result["benefitsModal"] = [
            'enable' => $this->getConfigData('onepage/benefits_modal/enable') == "1",
            'image' => $mediaUrl . 'theme_customization/' . $this->getConfigData('onepage/benefits_modal/image')
        ];
        $result['ruleslabelsApplied'] = $this->ruleslabelsApplied();
        return $result;
    }

    /**
     * @return bool[]
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function ruleslabelsApplied(){

        $items = $this->checkoutSession->getQuote()->getItems();
        $fastShipping = false;
        $inStorePickup = false;
        $noRules=false;

        foreach($items as $item){

            $rules = $this->dynamicTagRules->shippingLabelsByProductSku($item->getSku());
            if($rules['fast']== true){
                $fastShipping = true;
            }
            if($rules['instore']==true){
                $inStorePickup= true;
            }
            if($rules['fast']== false && $rules['instore']==false){
                $noRules=true;
            }
        }

        $rules = [
            'fastShipping' => $fastShipping,
            'inStorePickup' => $inStorePickup,
            'noRules' => $noRules
        ];

        return $rules;
    }
}
