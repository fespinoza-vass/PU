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
use WolfSellers\Consecutive\Api\Data\SequentialInterfaceFactory;
use WolfSellers\Consecutive\Api\Data\SequentialSearchResultsInterfaceFactory;
use WolfSellers\Consecutive\Api\SequentialRepositoryInterface;
use WolfSellers\Consecutive\Model\ResourceModel\Sequential as ResourceSequential;
use WolfSellers\Consecutive\Model\ResourceModel\Sequential\CollectionFactory as SequentialCollectionFactory;

class SequentialRepository implements SequentialRepositoryInterface
{

    private $storeManager;

    protected $dataObjectProcessor;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    protected $sequentialCollectionFactory;

    protected $sequentialFactory;

    protected $searchResultsFactory;

    protected $extensionAttributesJoinProcessor;

    protected $resource;

    protected $dataObjectHelper;

    protected $dataSequentialFactory;


    /**
     * @param ResourceSequential $resource
     * @param SequentialFactory $sequentialFactory
     * @param SequentialInterfaceFactory $dataSequentialFactory
     * @param SequentialCollectionFactory $sequentialCollectionFactory
     * @param SequentialSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceSequential $resource,
        SequentialFactory $sequentialFactory,
        SequentialInterfaceFactory $dataSequentialFactory,
        SequentialCollectionFactory $sequentialCollectionFactory,
        SequentialSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->sequentialFactory = $sequentialFactory;
        $this->sequentialCollectionFactory = $sequentialCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSequentialFactory = $dataSequentialFactory;
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
        \WolfSellers\Consecutive\Api\Data\SequentialInterface $sequential
    ) {
        $sequentialData = $this->extensibleDataObjectConverter->toNestedArray(
            $sequential,
            [],
            \WolfSellers\Consecutive\Api\Data\SequentialInterface::class
        );

        $sequentialModel = $this->sequentialFactory->create()->setData($sequentialData);

        try {
            $this->resource->save($sequentialModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the sequential: %1',
                $exception->getMessage()
            ));
        }
        return $sequentialModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($sequentialId)
    {
        $sequential = $this->sequentialFactory->create();
        $this->resource->load($sequential, $sequentialId);
        if (!$sequential->getId()) {
            throw new NoSuchEntityException(__('Sequential with id "%1" does not exist.', $sequentialId));
        }
        return $sequential->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->sequentialCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \WolfSellers\Consecutive\Api\Data\SequentialInterface::class
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
        \WolfSellers\Consecutive\Api\Data\SequentialInterface $sequential
    ) {
        try {
            $sequentialModel = $this->sequentialFactory->create();
            $this->resource->load($sequentialModel, $sequential->getSequentialId());
            $this->resource->delete($sequentialModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Sequential: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sequentialId)
    {
        return $this->delete($this->get($sequentialId));
    }
}

