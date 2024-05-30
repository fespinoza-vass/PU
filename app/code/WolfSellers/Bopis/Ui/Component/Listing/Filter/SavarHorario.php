<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Ui\Component\Listing\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\Ui\Component\Filters\Type\Select;

class SavarHorario extends Select
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param FilterModifier $filterModifier
     * @param OptionSourceInterface|null $optionsProvider
     * @param array $components
     * @param array $data
     */
    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, \Magento\Framework\Api\FilterBuilder $filterBuilder, FilterModifier $filterModifier, OptionSourceInterface $optionsProvider = null, array $components = [], array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $filterBuilder, $filterModifier, $optionsProvider, $components, $data);
    }

    /**
     * @return void
     */
    protected function applyFilter(): void
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if (!empty($value) || is_numeric($value)) {
                if (is_array($value)) {
                    $conditionType = 'like';
                } else {
                    $dataType = $this->getData('config/dataType');
                    $conditionType = $dataType == 'multiselect' ? 'finset' : 'like';
                }
                $filter = $this->filterBuilder->setConditionType($conditionType)
                    ->setField($this->getName())
                    ->setValue('%' . $value . '%')
                    ->create();

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }
}
