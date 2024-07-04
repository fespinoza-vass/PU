<?php

namespace WolfSellers\Bopis\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Helper\RememberMeHelper;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class ShippingOptions implements ArrayInterface
{
    /** @var string  */
    const SUFFIX = 'urbano';

    /** @var \Magento\Shipping\Model\Carrier\AbstractCarrierInterface[] */
    private $allDeliveryMethods;

    /**
     * @param Config $_deliveryModelConfig
     * @param ScopeConfigInterface $_scopeConfig
     * @param Collection $_ordersCollection
     * @param LoggerInterface $logger
     * @param RememberMeHelper $rememberMeHelper
     */
    public function __construct(
        protected Config               $_deliveryModelConfig,
        protected ScopeConfigInterface $_scopeConfig,
        protected Collection           $_ordersCollection,
        protected LoggerInterface      $logger,
        protected RememberMeHelper     $rememberMeHelper
    )
    {
        $this->allDeliveryMethods = $this->_deliveryModelConfig->getAllCarriers();
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    /**
     * Get Shipping Methods options
     * @return array
     * @todo Make disabling fast shipping in bopis configurable
     */
    public function getOptions(): array
    {
        $options = [];

        $enableCarriers = $this->_deliveryModelConfig->getActiveCarriers();
        foreach ($enableCarriers as $deliveryCode => $deliveryModel) {
            $options[($this->isUrbano($deliveryCode) && !$this->allUrbanoOptionsAvailable())
                ? self::SUFFIX
                : $deliveryCode] = $this->getAlias($deliveryCode);
        }

        unset($options[AbstractBopisCollection::REGULAR_SHIPPING_METHOD]);

        return $options;
    }

    /**
     * @return array
     */
    public function getOptionsOrderBased(): array
    {
        $mainOrders = $this->_ordersCollection
            ->distinct(true)
            ->addAttributeToSelect('shipping_method')
            ->addAttributeToSelect('shipping_description')
            ->load();

        $options = [];

        try {
            foreach ($mainOrders as $data) {
                $methodCode = $data->getShippingMethod();
                $options[($this->isUrbano($methodCode) && !$this->allUrbanoOptionsAvailable())
                    ? self::SUFFIX
                    : $methodCode] = $this->getAlias($methodCode);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        $options[''] = 'Todo';

        return $options;
    }

    /**
     * @param $methodCode
     * @return mixed
     */
    private function getAlias($methodCode)
    {
        foreach ($this->allDeliveryMethods as $deliveryCode => $deliveryModel) {
            if (str_contains($methodCode, $deliveryCode)) {
                $deliveryTitle = $this->_scopeConfig->getValue('carriers/' . $deliveryCode . '/title');

                if ($this->isUrbano($methodCode) && $this->allUrbanoOptionsAvailable()) {
                    $suffix = explode('_', $methodCode);
                    $deliveryTitle = $deliveryTitle . (isset($suffix[1]) ? ' - ' . $suffix[1] : '');
                }

                return $deliveryTitle;
            }
        }

        return $methodCode;
    }

    /**
     * @param $methodCode
     * @return bool
     */
    private function isUrbano($methodCode)
    {
        if (strstr($methodCode, self::SUFFIX) != false) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function allUrbanoOptionsAvailable()
    {
        $role = $this->rememberMeHelper->getCurrentUserRole();

        if ($role == AbstractBopisCollection::BOPIS_SUPER_ADMIN) {
            return true;
        }

        return false;
    }
}
