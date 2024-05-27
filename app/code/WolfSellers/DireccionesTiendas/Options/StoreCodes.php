<?php

namespace WolfSellers\DireccionesTiendas\Options;

use Psr\Log\LoggerInterface;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;

use Magento\InventoryApi\Api\SourceRepositoryInterface;

class StoreCodes implements \Magento\Framework\Data\OptionSourceInterface
{
    private LoggerInterface $logger;
    private SourceRepositoryInterface $sourceRepository;

    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;


    public function __construct(
        LoggerInterface              $logger,
        SourceRepositoryInterface    $sourceRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    )
    {
        $this->logger = $logger;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    public function toOptionArray()
    {
        $result = [];
        try {
            /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria = $searchCriteriaBuilder->create();
            $sources = $this->sourceRepository->getList($searchCriteria)->getItems();
            foreach ($sources as $source) {
                $this->logger->info('source->getName(): ' . $source->getName() . ' source->getSourceCode(): ' . $source->getSourceCode());
                $result[] = [
                    'value' => $source->getSourceCode(),
                    'label' => '[' . $source->getSourceCode() . '] ' . $source->getName()
                ];
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }
}
