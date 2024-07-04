<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Api\Data;

interface BopisInterface
{

    const ENTITY_ID = 'entity_id';
    const QUOTE_ID = 'quote_id';
    const TYPE = 'type';
    const ADDRESS_FORMATTED = 'address_formatted';
    const ADDRESS_OBJECT = 'address_object';
    const STORE = 'store';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getId();

    /**
     * Set entity_id
     * @param string $id
     * @return BopisInterface
     */
    public function setId($id);

    /**
     * Get quote_id
     * @return string|null
     */
    public function getQuoteId();

    /**
     * Set quote_id
     * @param string $quoteId
     * @return BopisInterface
     */
    public function setQuoteId($quoteId);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return BopisInterface
     */
    public function setType($type);

    /**
     * Get address_formatted
     * @return string|null
     */
    public function getAddressFormatted();

    /**
     * Set address_formatted
     * @param string $addressFormatted
     * @return BopisInterface
     */
    public function setAddressFormatted($addressFormatted);

    /**
     * Get address_object
     * @return string|null
     */
    public function getAddressObject();

    /**
     * Set address_object
     * @param string $addressObject
     * @return BopisInterface
     */
    public function setAddressObject($addressObject);

    /**
     * Get store
     * @return string|null
     */
    public function getStore();

    /**
     * Set store
     * @param string $store
     * @return BopisInterface
     */
    public function setStore($store);
}

