<?php

namespace adobe\adobeSignCheckout\Model\Config\Source;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\Data\CategoryTreeInterface;
use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use \Magento\Framework\Data\OptionSourceInterface;

class CategoryOptions implements OptionSourceInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var CategoryManagementInterface
     */
    private $categoryManagement;
    /**
     * @var ExtensibleDataObjectConverter
     */
    private $objectConverter;

    /**
     * Categories constructor.
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param CategoryManagementInterface $categoryManagement
     * @param ExtensibleDataObjectConverter $objectConverter
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        CategoryManagementInterface $categoryManagement,
        ExtensibleDataObjectConverter $objectConverter
    )
    {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->categoryManagement = $categoryManagement;
        $this->objectConverter = $objectConverter;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $data = [];
        $rootCategory = $this->getRootCategory();

        /** @var CategoryTreeInterface $tree */
        $tree = $this->categoryManagement->getTree($rootCategory, 3);
        $categoryArray = $this->objectConverter->toNestedArray($tree, [], CategoryTreeInterface::class);
        if (count($categoryArray)) {
            $data[] = ['value' => $categoryArray["id"], 'label' => $categoryArray["name"]];
            $this->getArray($data, $categoryArray["children_data"], 1);
        }
        return $data;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRootCategory()
    {
        $websiteId = $this->request->getParam("website", null);
        $store = $this->storeManager->getStore();
        if ($websiteId) {
            /** @var \Magento\Store\Model\Website $website */
            $website = $this->storeManager->getWebsite($websiteId);
            $store = $website->getDefaultStore();
        }
        return $store->getRootCategoryId();
    }

    public function getArray(&$data, $array, $level = 0)
    {
        foreach ($array as $category) {
            $arrow = str_repeat("---", $level);
            $data[] = ['value' => $category["id"], 'label' => __($arrow . " " . $category["name"])];
            if ($category["children_data"]) {
                $this->getArray($data, $category["children_data"], $level + 1);
            }
        }
    }
}
