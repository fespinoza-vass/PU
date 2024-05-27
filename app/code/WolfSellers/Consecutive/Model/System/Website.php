<?php

namespace WolfSellers\Consecutive\Model\System;

use Magento\Framework\Data\OptionSourceInterface;

class Website extends \Magento\Framework\DataObject implements OptionSourceInterface
{
    /**
     * Website collection
     * websiteId => \Magento\Store\Model\Website
     *
     * @var array
     */
    protected $_websiteCollection = [];

    /**
     * Store collection
     * storeId => \Magento\Store\Model\Store
     *
     * @var array
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Init model
     * Load Website, Group and Store collections
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
        $this->_websiteCollection = $this->_storeManager->getWebsites();

    }

    /**
     * Retrieve websites values for form
     *
     * @param bool $empty
     * @param bool $all
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getWebsiteValuesForForm($empty = false, $all = false)
    {
        $options = [];
        if ($empty) {
            $options[] = ['label' => '', 'value' => ''];
        }

        foreach ($this->_websiteCollection as $website) {
                $options[] = array('value'=>$website->getId(), 'label'=>$website->getName());
        }

        return $options;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getWebsiteValuesForForm();
    }
}
