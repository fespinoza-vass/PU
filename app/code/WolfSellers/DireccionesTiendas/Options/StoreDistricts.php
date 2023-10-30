<?php

namespace WolfSellers\DireccionesTiendas\Options;

use Psr\Log\LoggerInterface;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;

use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface;

class StoreDistricts extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    private LoggerInterface $logger;
    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;
    private DireccionesTiendasRepositoryInterface $tiendasRepository;


    public function __construct(
        LoggerInterface              $logger,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        DireccionesTiendasRepositoryInterface $tiendasRepository
    )
    {
        $this->logger = $logger;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->tiendasRepository = $tiendasRepository;
    }


    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $result = [];
        try {
            /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria = $searchCriteriaBuilder->create();
            $tiendas = $this->tiendasRepository->getList($searchCriteria)->getItems();
            foreach ($tiendas as $tienda) {
                //$this->logger->info('source->getDistrito(): ' . $source->getDistrito() . ' source->getDireccionestiendasId(): ' . $source->getDireccionestiendasId());
                $result[] = [
                    'value' => $tienda->getDireccionestiendasId(),
                    'label' => $tienda->getDistrito()
                ];
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }
}
