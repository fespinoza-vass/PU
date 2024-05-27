<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model\Data;

use WolfSellers\Consecutive\Api\Data\SequentialInterface;

class Sequential extends \Magento\Framework\Model\AbstractExtensibleModel implements SequentialInterface
{

    /**
     * Get sequential_id
     * @return string|null
     */
    public function getSequentialId()
    {
        return $this->getData(self::SEQUENTIAL_ID);
    }

    /**
     * Set sequential_id
     * @param string $sequentialId
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setSequentialId($sequentialId)
    {
        return $this->setData(self::SEQUENTIAL_ID, $sequentialId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \WolfSellers\Consecutive\Api\Data\SequentialExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \WolfSellers\Consecutive\Api\Data\SequentialExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \WolfSellers\Consecutive\Api\Data\SequentialExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get start_number
     * @return string|null
     */
    public function getStartNumber()
    {
        return $this->getData(self::START_NUMBER);
    }

    /**
     * Set start_number
     * @param string $startNumber
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setStartNumber($startNumber)
    {
        return $this->setData(self::START_NUMBER, $startNumber);
    }

    /**
     * Get format
     * @return string|null
     */
    public function getFormat()
    {
        return $this->getData(self::FORMAT);
    }

    /**
     * Set format
     * @param string $format
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setFormat($format)
    {
        return $this->setData(self::FORMAT, $format);
    }

    /**
     * Get website_id
     * @return string|null
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * Set website_id
     * @param string $websiteId
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

