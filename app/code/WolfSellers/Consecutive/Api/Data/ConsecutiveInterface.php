<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Api\Data;

interface ConsecutiveInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const WEBSITE_ID = 'website_id';
    const CONSECUTIVE_NUMBER = 'consecutive_number';
    const CONSECUTIVE_ID = 'consecutive_id';

    /**
     * Get consecutive_id
     * @return string|null
     */
    public function getConsecutiveId();

    /**
     * Set consecutive_id
     * @param string $consecutiveId
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     */
    public function setConsecutiveId($consecutiveId);

    /**
     * Get consecutive_number
     * @return string|null
     */
    public function getConsecutiveNumber();

    /**
     * Set consecutive_number
     * @param string $consecutiveNumber
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     */
    public function setConsecutiveNumber($consecutiveNumber);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \WolfSellers\Consecutive\Api\Data\ConsecutiveExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \WolfSellers\Consecutive\Api\Data\ConsecutiveExtensionInterface $extensionAttributes
    );

    /**
     * Get website_id
     * @return string|null
     */
    public function getWebsiteId();

    /**
     * Set website_id
     * @param string $websiteId
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     */
    public function setWebsiteId($websiteId);
}

