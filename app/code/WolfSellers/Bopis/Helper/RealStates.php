<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class RealStates
{
    /** @var string */
    const ENVIO_RAPIDO = 'envio_rapido';

    /** @var string */
    const STATE_PATH = 'bopis/status/';

    /** @var array */
    protected array $states;

    /**
     * @param ScopeConfigInterface $_scopeConfig
     * @param Config $_eavConfig
     */
    public function __construct(
        protected ScopeConfigInterface $_scopeConfig,
        protected Config               $_eavConfig
    )
    {
        $this->initializeStates();
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfig($path): mixed
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($path, $storeScope);
    }

    /**
     * @param $shippingMethodCode
     * @return string
     */
    public function getShippingMethodTitle($shippingMethodCode): string
    {
        if (!$shippingMethodCode) return '';

        if ($shippingMethodCode == AbstractBopisCollection::FAST_SHIPPING_METHOD) {
            $code[0] = self::ENVIO_RAPIDO;
        } else {
            $code = explode('_', $shippingMethodCode);
        }

        return $this->_scopeConfig->getValue('carriers/' . $code[0] . '/title');
    }

    /**
     * @return array|array[]
     */
    public function getRealBopisStates(): array
    {
        return $this->states;
    }

    /**
     * Return real states for progress bar
     * @return void
     *
     */
    private function initializeStates(): void
    {
        $this->states = [
            'confirmed' => [
                'state' => '',
                'label' => 'Pedido Confirmado',
                'action' => '#',
                'menu' => 'listnewsorders'
            ],
            'preparing' => [
                'state' => '',
                'label' => 'Pedido Preparado',
                'action' => 'bopis/prepare/save',
                'menu' => 'listprocessingorders'
            ],
            'shipping' => [
                'state' => '',
                'label' => 'Pedido en camino',
                'action' => 'bopis/shipping/save',
                'menu' => 'listshippingorders'
            ],
            'readyforpickup' => [
                'state' => '',
                'label' => 'Pedido Listo para Recojo',
                'action' => 'bopis/readyforpickup/save',
                'menu' => 'listreadyforpickup'
            ],
            'complete' => [
                'state' => '',
                'label' => 'Pedido Entregado',
                'action' => false,
                'menu' => 'listcompleteorders'
            ]
        ];

        foreach ($this->states as $state => $data) {
            $this->states[$state]['state'] = $this->getConfig(self::STATE_PATH . $state);
        }
    }

    /**
     * @param string $getStatus
     * @return mixed|void
     */
    public function getMenuOption(string $getStatus)
    {
        foreach ($this->states as $state => $data) {
            if ($data['state'] == $getStatus) {
                return $data['menu'];
            }
        }
    }

    /**
     * @param string $getStatus
     * @return string|void
     */
    public function getStateLabel(string $getStatus)
    {
        foreach ($this->states as $state => $data) {
            if ($data['state'] == $getStatus) {
                return trim(str_replace('Pedido', '', $data['label']));
            }
        }
    }

    /**
     * @param $schedule
     * @return string
     */
    public function getSchedule($schedule)
    {
        return match ($schedule) {
            "12_4_hoy" => "Hoy de 12:00 - 16:00",
            "4_8_hoy" => "Hoy de 16:00 - 20:00",
            "12_4_manana" => "Mañana de 12:00 - 16:00",
            "4_8_manana" => "Mañana de 16:00 - 20:00",
            default => ""
        };
    }

    /**
     * @param $component
     * @param $attributeCode
     * @param $value
     * @return bool|string
     * @throws LocalizedException
     */
    public function getRealAddrOptionValue($component, $attributeCode, $value)
    {
        $attribute = $this->_eavConfig->getAttribute($component, $attributeCode);
        if (!$attribute) return '';

        $optionLabel = $attribute->getSource()->getOptionText($value);
        return $optionLabel ?? '';
    }
}
