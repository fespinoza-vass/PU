<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model\Data;

use WolfSellers\Consecutive\Api\Data\ConsecutiveInterface;

class Consecutive extends \Magento\Framework\Model\AbstractExtensibleModel implements ConsecutiveInterface
{

    /**
     * Get consecutive_id
     * @return string|null
     */
    public function getConsecutiveId()
    {
        return $this->getData(self::CONSECUTIVE_ID);
    }

    /**
     * Set consecutive_id
     * @param string $consecutiveId
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     */
    public function setConsecutiveId($consecutiveId)
    {
        return $this->setData(self::CONSECUTIVE_ID, $consecutiveId);
    }

    /**
     * Get consecutive_number
     * @return string|null
     */
    public function getConsecutiveNumber()
    {
        return $this->getData(self::CONSECUTIVE_NUMBER);
    }

    /**
     * Set consecutive_number
     * @param string $consecutiveNumber
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     */
    public function setConsecutiveNumber($consecutiveNumber)
    {
        return $this->setData(self::CONSECUTIVE_NUMBER, $consecutiveNumber);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \WolfSellers\Consecutive\Api\Data\ConsecutiveExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \WolfSellers\Consecutive\Api\Data\ConsecutiveExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }
}

