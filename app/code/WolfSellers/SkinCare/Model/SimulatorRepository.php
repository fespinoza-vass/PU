<?php

namespace WolfSellers\SkinCare\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\SkinCare\Api\Data\SimulatorInterface;
use WolfSellers\SkinCare\Api\Data\SimulatorSearchResultsInterfaceFactory;
use WolfSellers\SkinCare\Api\SimulatorRepositoryInterface;

class SimulatorRepository implements SimulatorRepositoryInterface
{
    const MAIN_TABLE = 'wolfsellers_simulator_results';
    private ResourceModel\Simulator $resourceModelSimulator;
    private SimulatorFactory $simulatorFactory;
    private ResourceModel\Simulator\CollectionFactory $collectionFactory;
    private SimulatorSearchResultsInterfaceFactory $searchResultsInterfaceFactory;
    private CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        ResourceModel\Simulator                   $resourceModelSimulator,
        SimulatorFactory                          $simulatorFactory,
        ResourceModel\Simulator\CollectionFactory $collectionFactory,
        SimulatorSearchResultsInterfaceFactory    $searchResultsInterfaceFactory,
        CollectionProcessorInterface              $collectionProcessor

    )
    {
        $this->resourceModelSimulator = $resourceModelSimulator;
        $this->simulatorFactory = $simulatorFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsInterfaceFactory = $searchResultsInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param SimulatorInterface $simulator
     * @return SimulatorInterface
     * @throws CouldNotSaveException
     */
    public function save(SimulatorInterface $simulator)
    {
        try {
            $this->resourceModelSimulator->save($simulator);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the simulator: %1', $exception->getMessage()));
        }
        return $simulator;
    }

    /**
     * @param $id
     * @return SimulatorInterface
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        $simulator = $this->simulatorFactory->create();
        $this->resourceModelSimulator->load($simulator, $id);
        if (!$simulator->getId()) {
            throw new NoSuchEntityException(__('simulator with id "%1" does not exist.', $id));
        }
        return $simulator;
    }

    /**
     * @param $formId
     * @return SimulatorInterface
     * @throws NoSuchEntityException
     */
    public function getByFormId($formId)
    {
        $simulator = $this->simulatorFactory->create();
        $this->resourceModelSimulator->load($simulator, $formId, "form_id");
        if (!$simulator->getId()) {
            throw new NoSuchEntityException(__('simulator with form_id "%1" does not exist.', $simulator));
        }
        return $simulator;
    }

    /**
     * @param $email
     * @return SimulatorInterface
     * @throws NoSuchEntityException
     */
    public function getByEmail($email)
    {
        $simulator = $this->simulatorFactory->create();
        $this->resourceModelSimulator->load($simulator, $email, "email");
        if (!$simulator->getId()) {
            throw new NoSuchEntityException(__('simulator with email "%1" does not exist.', $simulator));
        }
        return $simulator;
    }

    /**
     * @param $email
     * @return SimulatorInterface
     * @throws NoSuchEntityException
     */
    public function getByFormType($formId, $type)
    {
        $simulator = $this->simulatorFactory->create();
        try {
            $select = $this->resourceModelSimulator->getConnection()->select()->from(self::MAIN_TABLE)->where("form_id = ?", $formId)->where("type = ?", $type);
            $data = $this->resourceModelSimulator->getConnection()->fetchRow($select);
            if ($data) {
                $simulator->setData($data);
            }
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('simulator with email "%1" does not exist.', $simulator));
        }
        if (!$simulator->getId()) {
            throw new NoSuchEntityException(__('simulator with email "%1" does not exist.', $simulator));
        }
        return $simulator;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \WolfSellers\SkinCare\Api\Data\SimulatorSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param SimulatorInterface $simulator
     * @return true
     * @throws CouldNotDeleteException
     */
    public function delete(SimulatorInterface $simulator)
    {
        try {
            $simulatorModel = $this->simulatorFactory->create();
            $this->resourceModelSimulator->load($simulatorModel, $simulator->getId());
            $this->resourceModelSimulator->delete($simulatorModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the simulator: %1',$exception->getMessage()));
        }
        return true;
    }

    /**
     * @param $id
     * @return true
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }
}
