<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class RealStates
{
    /** @var string */
    const STATE_PATH = 'bopis/status/';

    /** @var array */
    protected array $states;

    /**
     * @param ScopeConfigInterface $_scopeConfig
     */
    public function __construct(
        protected ScopeConfigInterface $_scopeConfig
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
        $code = explode('_', $shippingMethodCode);
       // return $this->_scopeConfig->getValue('carriers/' . $code[0] . '/title');
        return "";
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
            'pending' => [
                'state' => '',
                'label' => 'Pendiente',
                'action' => '#',
                'menu' => 'listnewsorders'
            ],
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
}
