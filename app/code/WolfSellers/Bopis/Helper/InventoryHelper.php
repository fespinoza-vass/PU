<?php

namespace WolfSellers\Bopis\Helper;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class InventoryHelper extends AbstractHelper
{
    private Session $checkoutSession;
    private BopisRepositoryInterface $bopisRepository;
    private Inventory $inventoryHelper;
    private Config $config;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        BopisRepositoryInterface $bopisRepository,
        Inventory $inventoryHelper,
        Config $config
    )
    {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->bopisRepository = $bopisRepository;
        $this->inventoryHelper = $inventoryHelper;
        $this->config = $config;
    }

    public function canContinue(){
        if (!$this->config->isActive()){
            return true;
        }
        return $this->checkoutSession->getCanContinue();
    }

    public function isInvalid($sku){
        if (!$this->checkoutSession->getInvalidItems()) return false;
        return in_array($sku, $this->checkoutSession->getInvalidItems());
    }

    public function validate($quoteId, $items){
        try{
            $canContinue = true;
            $bopis = $this->bopisRepository->getByQuoteId($quoteId);
            if ($bopis->getType() != "delivery"){
                $this->checkoutSession->setWasValidated(false);
                $skus = array_map(function ($item){return $item->getSku();},  $items);
                $response = $this->inventoryHelper->getCartInventory($skus, $bopis->getStore());
                $skusWInventory = array_map(function($item){
                    return ["sku" => $item['sku'], "qty" => $item['quantity']];
                }, $response['results']);

                $invalidItems = [];
                foreach ($items as $item) {
                    $responseItem = null;
                    foreach ($skusWInventory as $el) {
                        if ($el['sku'] == $item->getSku()){
                            $responseItem = $el;
                            break;
                        }
                    }
                    if ($responseItem == null || $responseItem['qty'] < $item->getQty()){
                        $invalidItems[] = $item->getSku();
                    }
                }

                if (!empty($invalidItems)) $canContinue = false;

                $this->checkoutSession->setValidatedItems($skus);
                $this->checkoutSession->setCanContinue($canContinue);
                $this->checkoutSession->setInvalidItems($invalidItems);
                $this->checkoutSession->setWasValidated(true);

            }else{
                $this->checkoutSession->setWasValidated(false);
                $this->checkoutSession->setInvalidItems([]);
                $this->checkoutSession->setCanContinue($canContinue);
            }
        }catch (Exception $exception){

        }

        return $canContinue;
    }

    public function isBopis($quoteId){
        try{
            $bopis = $this->bopisRepository->getByQuoteId($quoteId);
            return $bopis->getType() == 'store-pickup';
        }catch (Exception $exception){
            return false;
        }
    }

}
