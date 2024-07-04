<?php

namespace WolfSellers\Bopis\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;

class ConfigProvider implements ConfigProviderInterface
{
    private Session $checkoutSession;
    protected $logger;

    /**
     * @param Session $checkoutSession
     */
    public function __construct
    (
        Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/bopis.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    public function getConfig()
    {
        $additionalVariables['cartHaveBundle'] = $this->haveProductBundleInCart();
        return $additionalVariables;
    }

    public function haveProductBundleInCart(){
        $quote = $this->checkoutSession->getQuote();
        $this->logger->debug("QUOTE COUNT ".$quote->getItemsCount());
        if (!$quote->getItemsCount()){
            return true;
        }
        foreach ($quote->getAllVisibleItems() AS $item){
            if ($item->getProduct()->getTypeId() == "bundle"){
                return true;
            }
        }
        return false;
    }

}
