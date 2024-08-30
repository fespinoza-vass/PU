<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Model;

use Magento\Framework\Model\AbstractModel;
use WolfSellers\Bopis\Api\Data\BopisInterface;

class Bopis extends AbstractModel implements BopisInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Bopis::class);
    }

    /**
     * @inheritDoc
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getAddressFormatted()
    {
        return $this->getData(self::ADDRESS_FORMATTED);
    }

    /**
     * @inheritDoc
     */
    public function setAddressFormatted($addressFormatted)
    {
        return $this->setData(self::ADDRESS_FORMATTED, $addressFormatted);
    }

    /**
     * @inheritDoc
     */
    public function getAddressObject()
    {
        return $this->getData(self::ADDRESS_OBJECT);
    }

    /**
     * @inheritDoc
     */
    public function setAddressObject($addressObject)
    {
        return $this->setData(self::ADDRESS_OBJECT, $addressObject);
    }

    /**
     * @inheritDoc
     */
    public function getStore()
    {
        return $this->getData(self::STORE);
    }

    /**
     * @inheritDoc
     */
    public function setStore($store)
    {
        return $this->setData(self::STORE, $store);
    }
}

