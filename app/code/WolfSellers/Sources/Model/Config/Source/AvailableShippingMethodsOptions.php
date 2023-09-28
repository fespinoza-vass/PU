<?php

namespace WolfSellers\Sources\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config;

class AvailableShippingMethodsOptions implements OptionSourceInterface
{
    /** @var string */
    const FORCED_IN_STORE_CODE = 'forced_instore';

    /** @var string */
    const FORCED_IN_STORE_TITLE = 'Retiro en tienda por falta de stock';

    /**
     * @param ScopeConfigInterface $_scopeConfig
     * @param Config $_shippingModelConfig
     */
    public function __construct(
        protected ScopeConfigInterface $_scopeConfig,
        protected Config               $_shippingModelConfig
    )
    {
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $shipments = $this->_shippingModelConfig->getActiveCarriers();

        $options[] = [
            'label' => self::FORCED_IN_STORE_TITLE,
            'value' => self::FORCED_IN_STORE_CODE,
        ];

        foreach ($shipments as $shippingCode => $shippingModel) {
            $path = sprintf('carriers/%s/title', $shippingCode);

            $shippingTitle = $this->_scopeConfig->getValue($path);

            $options[] = [
                'label' => $shippingTitle,
                'value' => $shippingCode,
            ];
        }

        return $options;
    }

}
