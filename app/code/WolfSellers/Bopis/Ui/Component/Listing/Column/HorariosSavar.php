<?php

namespace WolfSellers\Bopis\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class HorariosSavar extends Column
{
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
                $item[$this->getData('name')] = $this->getSchedule($horario);
            }
        }

        return $dataSource;
    }

    /**
     * @param $schedule
     * @return string|void
     */
    protected function getSchedule($schedule)
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
