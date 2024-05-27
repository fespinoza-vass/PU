<?php

namespace WolfSellers\Bopis\Plugin\Checkout\Controller\Cart;

use Closure;
use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Controller\Cart\Add;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\Store\Model\StoreManagerInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Api\Data\BopisInterface;
use WolfSellers\Bopis\Helper\Config;

class AddPlugin extends Add
{

    private GetSourceItemsBySkuInterface $sourceItemsBySku;
    private BopisRepositoryInterface $bopisRepository;
    private Escaper $escaper;
    private BopisInterface $bopis;
    private Config $config;

    public function __construct(
        Context              $context,
        ScopeConfigInterface $scopeConfig,
        Session  $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        Escaper $escaper,
        BopisRepositoryInterface $bopisRepository,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        Config $config
    )
    {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $productRepository);
        $this->escaper = $escaper;
        $this->bopisRepository = $bopisRepository;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->config = $config;
    }

    public function aroundExecute(
        Add $subject,
        Closure $proceed
    ) {
        if (!$this->config->isActive()){
            return $proceed();
        }
        if ($this->hasItemBudle()){
            return $proceed();
        }
        if ($this->canContinue($subject)){
            return $proceed();
        }
        $this->messageManager->addNoticeMessage($this->escaper->escapeHtml(__("Sin inventario disponible en la tienda seleccionada.")));
        $url = $this->_checkoutSession->getRedirectUrl(true);
        return $this->goBack($url);
    }

    protected function canContinue($subject): bool{
        $this->setBopis();

        $product = $this->_initProduct();
        $qty = $subject->getRequest()->getParam('qty');

        $quoteItem = $this->_checkoutSession->getQuote()->getItemByProduct($product);
        if ($quoteItem){
            $qty += $quoteItem->getQty();
        }

        foreach ($this->sourceItemsBySku->execute($product->getSku()) as $source) {
            if ($source->getSourceCode() == $this->bopis->getStore()){
                if ((int)$source->getQuantity() >= $qty){
                    return true;
                }
            }
        }
        return false;
    }

    protected function setBopis(){
        try{
            $this->_checkoutSession->setValidatedItems([]);
            $this->_checkoutSession->setWasValidated(false);
            $this->bopis = $this->bopisRepository->getByQuoteId($this->_checkoutSession->getQuoteId());
        }catch (Exception $exception){
            $this->bopis = null;
        }
    }

    protected function hasItemBudle(){
        $product = $this->_initProduct();
        if ($product->getTypeId() == "bundle") {
            return true;
        }
        return false;
    }

}
