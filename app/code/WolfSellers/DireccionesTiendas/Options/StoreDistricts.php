<?php

namespace WolfSellers\DireccionesTiendas\Options;

use Psr\Log\LoggerInterface;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface;

class StoreDistricts extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /** @var LoggerInterface  */
    private LoggerInterface $logger;
    /** @var SearchCriteriaBuilderFactory  */
    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;
    /** @var SourceRepositoryInterface  */
    private SourceRepositoryInterface $sourceRepository;
    /** @var DireccionesTiendasRepositoryInterface  */
    private DireccionesTiendasRepositoryInterface $tiendasRepository;

    /** @var array  */
    protected array $allSources = [];


    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SourceRepositoryInterface $sourceRepository
     * @param DireccionesTiendasRepositoryInterface $tiendasRepository
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        LoggerInterface              $logger,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SourceRepositoryInterface $sourceRepository,
        DireccionesTiendasRepositoryInterface $tiendasRepository
    )
    {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->logger = $logger;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sourceRepository = $sourceRepository;
        $this->tiendasRepository = $tiendasRepository;
    }


    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false): array
    {
        $result = [];
        try {
            $this->getAllSources();
            /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria = $searchCriteriaBuilder->create();
            $tiendas = $this->tiendasRepository->getList($searchCriteria)->getItems();
            foreach ($tiendas as $tienda) {
                //$this->logger->info('source->getDistrito(): ' . $source->getDistrito() . ' source->getDireccionestiendasId(): ' . $source->getDireccionestiendasId());
                if($this->allSources[$tienda->getTienda()]['region_id']){
                    $result[] = [
                        'value' => $tienda->getDireccionestiendasId(),
                        'label' => $tienda->getDistrito(),
                        'region_id' => $this->allSources[$tienda->getTienda()]['region_id'] ?? '',
                        'region' => $this->allSources[$tienda->getTienda()]['region'] ?? ''
                    ];
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function getAllSources(): void
    {
        $sources = $this->sourceRepository->getList();
        foreach ($sources->getItems() as $source){
            $this->allSources[$source->getSourceCode()] = $source->getData();
        }
    }
}
