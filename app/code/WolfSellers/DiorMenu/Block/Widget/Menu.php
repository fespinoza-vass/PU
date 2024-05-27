<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-05-02
 * Time: 21:18
 */

declare(strict_types=1);

namespace WolfSellers\DiorMenu\Block\Widget;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Search\Model\QueryFactory;
use Magento\Widget\Block\BlockInterface;
use WolfSellers\DiorMenu\Model\Source\Gender;
use WolfSellers\DiorMenu\Model\Source\Manufacturer;

/**
 * Dior Menu Widget.
 */
class Menu extends Template implements BlockInterface
{
    protected $_template = 'widget/menu.phtml';

    /** @var array */
    private array $items;

    /** @var ProductCollectionFactory */
    private ProductCollectionFactory $productCollectionFactory;

    /** @var CategoryRepository */
    private CategoryRepository $categoryRepository;

    /**
     * @param Template\Context $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryRepository $categoryRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ProductCollectionFactory $productCollectionFactory,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get manufacturer.
     *
     * @return array|mixed|null
     */
    public function getManufacturer()
    {
        return $this->getData('manufacturer');
    }

    /**
     * Get gender.
     *
     * @return array|mixed|null
     */
    public function getGender()
    {
        return $this->getData('gender');
    }

    /**
     * Get category id.
     *
     * @return int
     */
    public function getCategoryId(): int
    {
        [, $categoryId]  = explode('/', $this->getDataByKey('id_path'));

        return (int) $categoryId;
    }

    /**
     * Get items.
     *
     * @return array
     */
    public function getItems(): array
    {
        if (!isset($this->items)) {
            $filterItems = $this->getFilterItems();
            $urlParams = [
                'cat' => $this->getCategoryId(),
                Manufacturer::ATTR_CODE => $this->getManufacturer(),
            ];

            if ($gender = $this->getGender()) {
                $urlParams[Gender::ATTR_CODE] = $gender;
            }

            $this->items = [];
            foreach ($filterItems as $itemValue) {
                $urlParams[QueryFactory::QUERY_VAR_NAME] = $itemValue;

                $this->items[] = [
                    'url' => $this->_urlBuilder->getUrl('catalogsearch/result', [
                        '_query' => $urlParams,
                    ]),
                    'text' => $itemValue,
                ];
            }

        }

        return $this->items;
    }

    /**
     * Get filter items by widget.
     *
     * @return array
     */
    private function getFilterItems(): array
    {
        $category = $this->getCategory();
        $columnShow = $this->getData('attr');

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect($columnShow);
        $collection->addCategoryFilter($category);
        $collection->addAttributeToFilter(Manufacturer::ATTR_CODE, $this->getManufacturer());
        $collection->setOrder($columnShow, 'ASC');

        if ($gender = $this->getGender()) {
            $collection->addAttributeToFilter(Gender::ATTR_CODE, $gender);
        }

        $columns = $collection->getColumnValues($columnShow);

        return array_unique(array_map('trim', $columns));
    }

    /**
     * Get category.
     *
     * @return Category|null
     */
    private function getCategory(): ?Category
    {
        return $this->categoryRepository->get($this->getCategoryId());
    }
}
