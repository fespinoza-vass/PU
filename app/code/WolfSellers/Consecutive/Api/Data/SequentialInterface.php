<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Api\Data;
interface SequentialInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const WEBSITE_ID = 'website_id';
    const START_NUMBER = 'start_number';
    const UPDATED_AT = 'updated_at';
    const NAME = 'name';
    const SEQUENTIAL_ID = 'sequential_id';
    const CREATED_AT = 'created_at';
    const FORMAT = 'format';

    /**
     * Get sequential_id
     * @return string|null
     */
    public function getSequentialId();

    /**
     * Set sequential_id
     * @param string $sequentialId
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setSequentialId($sequentialId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \WolfSellers\Consecutive\Api\Data\SequentialExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \WolfSellers\Consecutive\Api\Data\SequentialExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \WolfSellers\Consecutive\Api\Data\SequentialExtensionInterface $extensionAttributes
    );

    /**
     * Get start_number
     * @return string|null
     */
    public function getStartNumber();

    /**
     * Set start_number
     * @param string $startNumber
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setStartNumber($startNumber);

    /**
     * Get format
     * @return string|null
     */
    public function getFormat();

    /**
     * Set format
     * @param string $format
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setFormat($format);

    /**
     * Get website_id
     * @return string|null
     */
    public function getWebsiteId();

    /**
     * Set website_id
     * @param string $websiteId
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     */
    public function setUpdatedAt($updatedAt);
}

