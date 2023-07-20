<?php

namespace WolfSellers\Bopis\Plugin\Checkout\Controller\Cart;

use Closure;
use Exception;
use Magento\Checkout\Controller\Cart\UpdateItemQty;
use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Escaper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Api\Data\BopisInterface;
use WolfSellers\Bopis\Helper\Config;

class UpdateItemQtyPlugin extends UpdateItemQty
{

    private GetSourceItemsBySkuInterface $sourceItemsBySku;
    private BopisRepositoryInterface $bopisRepository;
    private Escaper $escaper;
    private BopisInterface $bopis;
    private Session $checkoutSession;
    private Json $json;
    private Config $config;

    protected $logger;

    public function __construct(
        Context $context,
        RequestQuantityProcessor $quantityProcessor,
        FormKeyValidator $formKeyValidator,
        Session $checkoutSession,
        Json $json,
        LoggerInterface $logger,
        Escaper $escaper,
        BopisRepositoryInterface $bopisRepository,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        Config $config
    )
    {
        parent::__construct($context, $quantityProcessor, $formKeyValidator, $checkoutSession, $json, $logger);
        $this->escaper = $escaper;
        $this->bopisRepository = $bopisRepository;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->checkoutSession = $checkoutSession;
        $this->json = $json;
        $this->config = $config;
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/bopis.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    public function aroundExecute(
        UpdateItemQty $subject,
        Closure $proceed
    ) {

        if (!$this->config->isActive()){
            return $proceed();
        }

        if ($this->canContinue()){
            return $proceed();
        }
        $this->messageManager->addNoticeMessage($this->escaper->escapeHtml(__("Sin inventario disponible en la tienda seleccionada.")));
        $this->jsonResponse();
    }

    protected function canContinue(): bool{
        $this->setBopis();

        $cartData = $this->getRequest()->getParam('cart');
        $quote = $this->checkoutSession->getQuote();

        foreach ($cartData as $itemId => $itemInfo) {
            $item = $quote->getItemById($itemId);
            $qty = isset($itemInfo['qty']) ? (double) $itemInfo['qty'] : 0;
            if ($item) {
                foreach ($this->sourceItemsBySku->execute($item->getSku()) as $source) {
                    if ($source->getSourceCode() == $this->bopis->getStore() AND (int)$source->getQuantity() >= $qty){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    protected function setBopis():bool{
        try{
            $this->checkoutSession->setValidatedItems([]);
            $this->checkoutSession->setWasValidated(false);
            $this->bopis = $this->bopisRepository->getByQuoteId($this->checkoutSession->getQuoteId());
            return true;
        }catch (Exception $exception){
            $this->bopis = null;
            $this->logger->debug($exception->getMessage());
            $this->logger->debug($exception->getTraceAsString());
            return false;
        }
    }

    private function jsonResponse()
    {
        $this->getResponse()->representJson($this->json->serialize(['success' => false]));
    }
}
