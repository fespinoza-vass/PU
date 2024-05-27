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
                $tpl = sprintf(
                    '<span class="grid-status %s">%s</span>',
                    $status,
                    ucfirst($this->toSpanish($status))
                );
                $item[$this->getData('name')] = $tpl;
            }
        }

        return $dataSource;
    }

    /**
     * @param $status
     * @return string
     */
    public function toSpanish($status): string
    {

        if ($status === null || !is_string($status)) {
            return '';
        }

        return match ($status) {
            'received' => 'Recibido',
            'confirmed_order' => 'Pedido confirmado',
            'prepared_order' => 'Pedido preparado',
            'processing' => 'Procesando',
            'fraud' => 'Sospecha de fraude',
            'pending_payment' => 'Pago pendiente',
            'payment_review' => 'Revisión de pagos',
            'pending' => 'Pendiente',
            'holded' => 'En espera',
            'order_delivered' => 'Pedido entregado',
            'order_on_the_way' => 'Pedido en camino',
            'complete' => 'Completa',
            'order_ready_for_pick_up' => 'Pedido listo para recojo',
            'closed' => 'Cerrada',
            'rejected' => 'Rechazada',
            'canceled' => 'Cancelada',
            default => $status,
        };
    }
}
