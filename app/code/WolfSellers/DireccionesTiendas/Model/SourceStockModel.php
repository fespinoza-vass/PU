<?php
namespace WolfSellers\DireccionesTiendas\Model;


use WolfSellers\OrderQR\Helper\SourceQuantityHelper;
use \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class SourceStockModel {

    /** @var SourceQuantityHelper */
    protected $_sourceQuantityHelper;


    /** @var MaskedQuoteIdToQuoteIdInterface  */
    protected $_maskedQuoteIdToQuoteId;

    public function __construct(
        SourceQuantityHelper $sourceQuantityHelper,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
    ) {
        $this->_maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->_sourceQuantityHelper = $sourceQuantityHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableStock($cartId, $sourceCode):bool
    {
        $cartId = $this->_maskedQuoteIdToQuoteId->execute($cartId);
        return $this->_sourceQuantityHelper->hasStockInSourceByCardId($cartId,$sourceCode);
    }
}
