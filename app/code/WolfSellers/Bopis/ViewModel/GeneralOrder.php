<?php

namespace WolfSellers\Bopis\ViewModel;

use WolfSellers\Bopis\Helper\RealStates;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class GeneralOrder implements ArgumentInterface
{
    /**
     * @param RealStates $_realStates
     * @param RedirectInterface $redirect
     * @param SourceRepositoryInterface $_sourceRepository
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     */
    public function __construct(
        protected RealStates                $_realStates,
        protected RedirectInterface         $redirect,
        protected SourceRepositoryInterface $_sourceRepository,
        protected SearchCriteriaBuilder     $_searchCriteriaBuilder,
    )
    {
    }

    /**
     * @param $shippingMethodCode
     * @return string
     */
    public function getShippingMethodTitle($shippingMethodCode)
    {
        return $this->_realStates->getShippingMethodTitle($shippingMethodCode);
    }

    /**
     * @param $status
     * @return string|null
     */
    public function getStateLabel($status): ?string
    {
        if (!$status) {
            return $status;
        }
        return $this->_realStates->getStateLabel($status);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * @param $sourceCode
     * @return string
     */
    public function getOrderSourceName($sourceCode): string
    {
        $this->_searchCriteriaBuilder->addFilter('source_code', $sourceCode);
        $searchCriteria = $this->_searchCriteriaBuilder->create();

        $searchCriteriaResult = $this->_sourceRepository->getList($searchCriteria);
        $sources = $searchCriteriaResult->getItems();

        $source = current($sources);

        if (!$source) return $sourceCode;

        return $source->getName();
    }

    /**
     * @param $schedule
     * @return string
     */
    public function getSchedule($schedule)
    {
        return match ($schedule){
            "12_4_hoy" => "12:00 - 16:00 Hoy",
            "4_8_hoy" => "16:00 - 20:00 Hoy",
            "12_4_manana" => "12:00 - 16:00 Mañana",
            "4_8_manana" => "16:00 - 20:00 Mañana",
            default => ""
        };
    }
}
