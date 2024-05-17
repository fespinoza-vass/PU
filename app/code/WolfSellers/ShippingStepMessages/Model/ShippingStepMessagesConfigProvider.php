<?php

namespace WolfSellers\ShippingStepMessages\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 *
 */
class ShippingStepMessagesConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \WolfSellers\ShippingStepMessages\Helper\Data
     */
    protected $helperData;

    /**
     * @param \WolfSellers\ShippingStepMessages\Helper\Data $helperData
     */
    public function __construct(
        \WolfSellers\ShippingStepMessages\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @return array[]
     */
    public function getConfig()
    {
        // Recuperar configuraciones para openings_1
        $configuredLocationsOne = $this->helperData->getConfiguredLocations();
        $sendingHoursOne = $this->helperData->getSendingHoursMessage();
        $deliveryTimeMessageOne = $this->helperData->getDeliveryTimeMessage();
        $restSendingHoursOne = $this->helperData->getRestSendingHoursMessage();
        $restDeliveryTimeMessageOne = $this->helperData->getRestDeliveryTimeMessage();

        // Recuperar configuraciones para openings_2
        $configuredLocationsTwo = $this->helperData->getConfiguredLocationsTwo();
        $sendingHoursTwo = $this->helperData->getSendingHoursMessageTwo();
        $deliveryTimeMessageTwo = $this->helperData->getDeliveryTimeMessageTwo();
        $restSendingHoursTwo = $this->helperData->getRestSendingHoursMessageTwo();
        $restDeliveryTimeMessageTwo = $this->helperData->getRestDeliveryTimeMessageTwo();

        return [
            'shippingSettings' => [
                'openings_1' => [
                    'configuredLocations' => implode(',', $configuredLocationsOne),
                    'sendingHours' => $sendingHoursOne,
                    'deliveryTimeMessage' => $deliveryTimeMessageOne,
                    'restSendingHours' => $restSendingHoursOne,
                    'restDeliveryTimeMessage' => $restDeliveryTimeMessageOne,
                ],
                'openings_2' => [
                    'configuredLocations' => implode(',', $configuredLocationsTwo),
                    'sendingHours' => $sendingHoursTwo,
                    'deliveryTimeMessage' => $deliveryTimeMessageTwo,
                    'restSendingHours' => $restSendingHoursTwo,
                    'restDeliveryTimeMessage' => $restDeliveryTimeMessageTwo,
                ]
            ],
        ];
    }
}
