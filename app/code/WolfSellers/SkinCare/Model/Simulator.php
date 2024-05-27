<?php

namespace WolfSellers\SkinCare\Model;

use Magento\Framework\Model\AbstractModel;
use WolfSellers\SkinCare\Api\Data\SimulatorInterface;

class Simulator extends AbstractModel implements SimulatorInterface
{
    public function _construct()
    {
        $this->_init(ResourceModel\Simulator::class);
    }

    /**
     * @inheritDoc
     */
    public function getFormId()
    {
        return $this->getData(self::FORM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setFormId($formId)
    {
        return $this->setData(self::FORM_ID, $formId);
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
    public function getPercentage()
    {
        return $this->getData(self::PERCENTAGE);
    }

    /**
     * @inheritDoc
     */
    public function setPercentage($percentage)
    {
        return $this->setData(self::PERCENTAGE, $percentage);
    }

    /**
     * @inheritDoc
     */
    public function getProductIds()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    /**
     * @inheritDoc
     */
    public function setProductIds($productIds)
    {
        return $this->setData(self::PRODUCT_IDS, $productIds);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }
}
