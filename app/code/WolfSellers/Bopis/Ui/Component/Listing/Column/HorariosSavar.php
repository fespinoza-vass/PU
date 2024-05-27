<?php

namespace WolfSellers\Bopis\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use WolfSellers\Bopis\Helper\RealStates;

class HorariosSavar extends Column
{
    /** @var RealStates */
    protected RealStates $realStates;

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, RealStates $realStates, array $components = [], array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->realStates = $realStates;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $horario = $item[$this->getData('name')];
                $formated = $this->realStates->getSchedule($horario);
                $item[$this->getData('name')] = $formated != "" ? $formated : 'NA';
            }
        }

        return $dataSource;
    }
}
