<?php

namespace WolfSellers\SkinCare\Api\Data;

interface SimulatorInterface
{

    const FORM_ID = 'form_id';
    const TYPE = 'type';
    const PERCENTAGE = 'percentage';
    const PRODUCT_IDS = 'product_ids';
    const EMAIL = 'email';

    /**
     * @return string|null
     */
    public function getFormId();

    /**
     * @param $formId
     * @return SimulatorInterface
     */
    public function setFormId($formId);

    /**
     * @return string|null
     */
    public function getType();

    /**
     * @param $type
     * @return SimulatorInterface
     */
    public function setType($type);

    /**
     * @return string|null
     */
    public function getPercentage();

    /**
     * @param $percentage
     * @return SimulatorInterface
     */
    public function setPercentage($percentage);

    /**
     * @return string|null
     */
    public function getProductIds();

    /**
     * @param $productIds
     * @return SimulatorInterface
     */
    public function setProductIds($productIds);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param $email
     * @return SimulatorInterface
     */
    public function setEmail($email);

}
