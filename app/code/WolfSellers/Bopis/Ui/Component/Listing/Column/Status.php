<?php


namespace WolfSellers\Bopis\Ui\Component\Listing\Column;


use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
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
                $status = $item[$this->getData('name')];
                if($item["is_new"] == 1)  {
                    $status = "new";
                }
                $tpl = sprintf(
                    '<span class="grid-status %s">%s</span>',
                    $status,
                    ucfirst($status)
                );
                $item[$this->getData('name')] = $tpl;
            }
        }

        return $dataSource;
    }
}
