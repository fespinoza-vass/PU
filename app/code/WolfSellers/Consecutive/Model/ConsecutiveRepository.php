<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use WolfSellers\Consecutive\Api\ConsecutiveRepositoryInterface;
use WolfSellers\Consecutive\Api\Data\ConsecutiveInterfaceFactory;
use WolfSellers\Consecutive\Api\Data\ConsecutiveSearchResultsInterfaceFactory;
use WolfSellers\Consecutive\Model\ResourceModel\Consecutive as ResourceConsecutive;
use WolfSellers\Consecutive\Model\ResourceModel\Consecutive\CollectionFactory as ConsecutiveCollectionFactory;

class ConsecutiveRepository implements ConsecutiveRepositoryInterface
{

    private $storeManager;

    protected $dataObjectProcessor;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    protected $consecutiveCollectionFactory;

    protected $searchResultsFactory;

    protected $extensionAttributesJoinProcessor;

    protected $resource;

    protected $dataObjectHelper;

    protected $dataConsecutiveFactory;

    protected $consecutiveFactory;


    /**
     * @param ResourceConsecutive $resource
     * @param ConsecutiveFactory $consecutiveFactory
     * @param ConsecutiveInterfaceFactory $dataConsecutiveFactory
     * @param ConsecutiveCollectionFactory $consecutiveCollectionFactory
     * @param ConsecutiveSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceConsecutive $resource,
        ConsecutiveFactory $consecutiveFactory,
        ConsecutiveInterfaceFactory $dataConsecutiveFactory,
        ConsecutiveCollectionFactory $consecutiveCollectionFactory,
        ConsecutiveSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->consecutiveFactory = $consecutiveFactory;
        $this->consecutiveCollectionFactory = $consecutiveCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataConsecutiveFactory = $dataConsecutiveFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface $consecutive
    ) {
        $consecutiveData = $this->extensibleDataObjectConverter->toNestedArray(
            $consecutive,
            [],
            \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface::class
        );

        $consecutiveModel = $this->consecutiveFactory->create()->setData($consecutiveData);

        try {
            $this->resource->save($consecutiveModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the consecutive: %1',
                $exception->getMessage()
            ));
        }
        return $consecutiveModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($consecutiveId)
    {
        $consecutive = $this->consecutiveFactory->create();
        $this->resource->load($consecutive, $consecutiveId);
        if (!$consecutive->getId()) {
            throw new NoSuchEntityException(__('consecutive with id "%1" does not exist.', $consecutiveId));
        }
        return $consecutive->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->consecutiveCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface $consecutive
    ) {
        try {
            $consecutiveModel = $this->consecutiveFactory->create();
            $this->resource->load($consecutiveModel, $consecutive->getConsecutiveId());
            $this->resource->delete($consecutiveModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the consecutive: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($consecutiveId)
    {
        return $this->delete($this->get($consecutiveId));
    }
}

