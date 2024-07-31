<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface;
use WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterfaceFactory;
use WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasSearchResultsInterfaceFactory;
use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface;
use WolfSellers\DireccionesTiendas\Model\ResourceModel\DireccionesTiendas as ResourceDireccionesTiendas;
use WolfSellers\DireccionesTiendas\Model\ResourceModel\DireccionesTiendas\CollectionFactory as DireccionesTiendasCollectionFactory;

class DireccionesTiendasRepository implements DireccionesTiendasRepositoryInterface
{

    /**
     * @var DireccionesTiendasInterfaceFactory
     */
    protected $direccionesTiendasFactory;

    /**
     * @var DireccionesTiendasCollectionFactory
     */
    protected $direccionesTiendasCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var DireccionesTiendas
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceDireccionesTiendas
     */
    protected $resource;


    /**
     * @param ResourceDireccionesTiendas $resource
     * @param DireccionesTiendasInterfaceFactory $direccionesTiendasFactory
     * @param DireccionesTiendasCollectionFactory $direccionesTiendasCollectionFactory
     * @param DireccionesTiendasSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceDireccionesTiendas $resource,
        DireccionesTiendasInterfaceFactory $direccionesTiendasFactory,
        DireccionesTiendasCollectionFactory $direccionesTiendasCollectionFactory,
        DireccionesTiendasSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->direccionesTiendasFactory = $direccionesTiendasFactory;
        $this->direccionesTiendasCollectionFactory = $direccionesTiendasCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        DireccionesTiendasInterface $direccionesTiendas
    ) {
        try {
            $this->resource->save($direccionesTiendas);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the direccionesTiendas: %1',
                $exception->getMessage()
            ));
        }
        return $direccionesTiendas;
    }

    /**
     * @inheritDoc
     */
    public function get($direccionesTiendasId)
    {
        $direccionesTiendas = $this->direccionesTiendasFactory->create();
        $this->resource->load($direccionesTiendas, $direccionesTiendasId);
        if (!$direccionesTiendas->getId()) {
            throw new NoSuchEntityException(__('DireccionesTiendas with id "%1" does not exist.', $direccionesTiendasId));
        }
        return $direccionesTiendas;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->direccionesTiendasCollectionFactory->create();
        
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
    public function delete(
        DireccionesTiendasInterface $direccionesTiendas
    ) {
        try {
            $direccionesTiendasModel = $this->direccionesTiendasFactory->create();
            $this->resource->load($direccionesTiendasModel, $direccionesTiendas->getDireccionestiendasId());
            $this->resource->delete($direccionesTiendasModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the DireccionesTiendas: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($direccionesTiendasId)
    {
        return $this->delete($this->get($direccionesTiendasId));
    }
}

