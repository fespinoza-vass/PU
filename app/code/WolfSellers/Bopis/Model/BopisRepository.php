<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Api\Data\BopisInterface;
use WolfSellers\Bopis\Api\Data\BopisSearchResultsInterfaceFactory;
use WolfSellers\Bopis\Model\ResourceModel\Bopis as ResourceCart;
use WolfSellers\Bopis\Model\ResourceModel\Bopis\CollectionFactory as BopisCollectionFactory;

class BopisRepository implements BopisRepositoryInterface
{

    /**
     * @var ResourceCart
     */
    protected $resource;

    /**
     * @var BopisFactory
     */
    protected $bopisFactory;

    /**
     * @var BopisCollectionFactory
     */
    protected $bopisCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Bopis
     */
    protected $searchResultsFactory;


    /**
     * @param ResourceCart $resource
     * @param BopisFactory $bopisFactory
     * @param BopisCollectionFactory $bopisCollectionFactory
     * @param BopisSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceCart $resource,
        BopisFactory $bopisFactory,
        BopisCollectionFactory $bopisCollectionFactory,
        BopisSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->bopisFactory = $bopisFactory;
        $this->bopisCollectionFactory = $bopisCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(BopisInterface $bopis)
    {
        try {
            $this->resource->save($bopis);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the bopis: %1', $exception->getMessage()));
        }
        return $bopis;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $bopis = $this->bopisFactory->create();
        $this->resource->load($bopis, $id);
        if (!$bopis->getId()) {
            throw new NoSuchEntityException(__('bopis with id "%1" does not exist.', $id));
        }
        return $bopis;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->bopisCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(BopisInterface $bopis)
    {
        try {
            $bopisModel = $this->bopisFactory->create();
            $this->resource->load($bopisModel, $bopis->getId());
            $this->resource->delete($bopisModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the bopis: %1',$exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }

    /**
     * @inheritDoc
     */
    public function getByQuoteId($quoteId)
    {
        $bopis = $this->bopisFactory->create();
        $this->resource->load($bopis, $quoteId, "quote_id");
        if (!$bopis->getId()) {
            throw new NoSuchEntityException(__('bopis with id "%1" does not exist.', $bopis));
        }
        return $bopis;
    }
}

