<?php

namespace WolfSellers\Bopis\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

class SourceOptions implements ArrayInterface
{

    /**
     * @param SourceRepositoryInterface $_sourceRepository
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected SourceRepositoryInterface $_sourceRepository,
        protected SearchCriteriaBuilder     $_searchCriteriaBuilder,
        protected LoggerInterface           $logger
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $this->_searchCriteriaBuilder->addFilter('enabled', true);
        $searchCriteria = $this->_searchCriteriaBuilder->create();

        $searchCriteriaResult = $this->_sourceRepository->getList($searchCriteria);
        $sources = $searchCriteriaResult->getItems();
        $options = [];

        try {
            foreach ($sources as $source) {
                $sourceCode = trim($source->getSourceCode());
                $options[$sourceCode] = $source->getName();
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        $options[''] = 'Todo';

        return $options;
    }
}
