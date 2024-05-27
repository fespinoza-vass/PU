<?php

namespace WolfSellers\StoreLocator\Model\Resolver;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\InventoryApi\Api\Data\SourceItemSearchResultsInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;

/**
 * Resolve Inventory Available.
 */
class InventoryAvailable implements ResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;
    /**
     * @var SourceItemRepositoryInterface
     */
    protected $_sourceItemRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceItemRepositoryInterface $sourceItemRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository
    ){
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sourceItemRepository = $sourceItemRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateInput($args);

        $this->_searchCriteriaBuilder->addFilter('source_code', $args['source_code'], 'eq');
        $searchCriteria = $this->_searchCriteriaBuilder->setPageSize($args['page_size'])
            ->setCurrentPage($args['page'])
            ->create();

        $searchCriteriaResult = $this->_sourceItemRepository->getList($searchCriteria);
        $items = [];

        foreach ($searchCriteriaResult->getItems() as $item) {
            $items[] = [
                'sku' => $item->getSku(),
                'quantity' => $item->getQuantity()
            ];
        }

        return [
            'items' => $items,
            'page_size' => $searchCriteriaResult->getSearchCriteria()->getPageSize(),
            'max_page' => $this->getMaxPage($args['page_size'], $searchCriteriaResult),
            'total_results' => $searchCriteriaResult->getTotalCount(),
            'current_page' => $searchCriteriaResult->getSearchCriteria()->getCurrentPage()
        ];
    }

    /**
     * Validate input.
     *
     * @param array $args
     * @return void
     * @throws GraphQlInputException
     */
    private function validateInput(array $args): void
    {
        if (empty($args['source_code']) ) {
            throw new GraphQlInputException(__('source_code value must be empty.'));
        }
        if ($args['page'] < 1) {
            throw new GraphQlInputException(__('page value must be greater than 0.'));
        }
        if ($args['page_size'] < 1) {
            throw new GraphQlInputException(__('page_size value must be greater than 0.'));
        }
    }

    /**
     * Get maximum number of pages.
     *
     * @param int $pageSize
     * @param SourceItemSearchResultsInterface $searchResult
     * @return int
     */
    private function getMaxPage(int $pageSize, SourceItemSearchResultsInterface $searchResult): int
    {
        if ($pageSize) {
            return ceil($searchResult->getTotalCount() / $pageSize);
        } else {
            return 0;
        }
    }
}