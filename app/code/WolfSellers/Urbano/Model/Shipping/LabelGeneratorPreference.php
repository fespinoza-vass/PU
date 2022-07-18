<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-03-16
 * Time: 18:11
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Model\Shipping;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Store\Model\ScopeInterface;

/**
 * Label Generator Preference.
 */
class LabelGeneratorPreference extends LabelGenerator
{
    /**
     * {@inheritDoc}
     */
    public function create(Shipment $shipment, RequestInterface $request)
    {
        $order = $shipment->getOrder();
        $carrier = $this->carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());
        if (!$carrier->isShippingLabelsAvailable()) {
            throw new LocalizedException(__('Shipping labels is not available.'));
        }
        $shipment->setPackages($request->getParam('packages'));
        $response = $this->labelFactory->create()->requestToShipment($shipment);

        if ($response->hasErrors()) {
            throw new LocalizedException(__($response->getErrors()));
        }

        if (!$response->hasInfo()) {
            throw new LocalizedException(__('Response info is not exist.'));
        }

        $labelsContent = [];
        $trackingNumbers = [];
        $info = $response->getInfo();
        foreach ($info as $inf) {
            if (!empty($inf['tracking_number'])) {
                $trackingNumbers[] = $inf['tracking_number'];
            }

            if (!empty($inf['label_content'])) {
                $labelsContent[] = $inf['label_content'];
            }
        }

        if (count($labelsContent) > 0) {
            $outputPdf = $this->combineLabelsPdf($labelsContent);
            $shipment->setShippingLabel($outputPdf->render());
        }

        $carrierCode = $carrier->getCarrierCode();
        $carrierTitle = (string) $this->scopeConfig->getValue(
            'carriers/'.$carrierCode.'/title',
            ScopeInterface::SCOPE_STORE,
            $shipment->getStoreId()
        );

        if (!empty($trackingNumbers)) {
            $this->addTrackingNumbersToShipment($shipment, $trackingNumbers, $carrierCode, $carrierTitle);
        }
    }

    /**
     * Add tracking numbers.
     *
     * @param Shipment $shipment
     * @param array $trackingNumbers
     * @param string $carrierCode
     * @param string $carrierTitle
     *
     * @return void
     */
    private function addTrackingNumbersToShipment(
        Shipment $shipment,
        array $trackingNumbers,
        string $carrierCode,
        string $carrierTitle
    ) {
        foreach ($trackingNumbers as $number) {
            if (is_array($number)) {
                $this->addTrackingNumbersToShipment($shipment, $number, $carrierCode, $carrierTitle);
            } else {
                $shipment->addTrack(
                    $this->trackFactory->create()
                        ->setNumber($number)
                        ->setCarrierCode($carrierCode)
                        ->setTitle($carrierTitle)
                );
            }
        }
    }
}
