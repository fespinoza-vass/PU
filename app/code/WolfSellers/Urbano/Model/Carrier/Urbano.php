<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-03-09
 * Time: 11:47
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Model\Carrier;

use Carbon\Carbon;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Model\Order\Shipment\Item;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Shipping\Model\Tracking\Result;
use WolfSellers\Urbano\Model\Source\Packaging;
use WolfSellers\Urbano\Service\ApiService;

/**
 * Urbano Carrier.
 *
 * @method ApiService getApiClient()
 */
class Urbano extends AbstractCarrierOnline implements CarrierInterface
{
    public const CODE = 'urbano';

    private const QUOTE_SERVICE_ID = '1';
    private const QUOTE_INSURANCE_ID = '3';

    private const METHOD_TERRESTRE = 'terrestre';
    private const METHOD_AEREO = 'aereo';

    /** @var string */
    protected $_code = self::CODE;

    /** @var Result */
    private Result $trackingResult;

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()) {
            return false;
        }

        $this->setRawRequest($request);
        $quotes = $this->getQuotes();

        $this->_logger->error("Urbano quotes:\n" . print_r($quotes, true) . "\n");
        if (!$quotes) {
            $this->_logger->error('Urbano: Not quotes available');

            return false;
        }

        $result = $this->_rateFactory->create();

        // Append quotes to result
        foreach ($quotes as $quote) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($quote['method']);
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice($quote['cost']);
            $method->setCost($quote['cost']);
            $method->setTime($quote['time']);

            $result->append($method);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function processAdditionalValidation(DataObject $request)
    {
        // Skip by item validation if there is no items in request
        if (empty($this->getAllItems($request))) {
            return false;
        }

        if (!$request->getDestPostcode() || !$request->getPostcode()) {
            return false;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedMethods(): array
    {
        $allowedMethods = [
            self::METHOD_TERRESTRE => '',
            self::METHOD_AEREO => '',
        ];

        foreach ($allowedMethods as $key => $title) {
            $allowedMethods[$key] = $this->getConfigData('name');
        }

        return $allowedMethods;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerTypes(DataObject $params = null): array
    {
        return [
            Packaging::PACKAGE => __('Paquetes'),
            Packaging::ENVELOPE => __('Sobres'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function requestToShipment($request)
    {
        $packages = $request->getPackages();

        if (!is_array($packages) || !$packages) {
            throw new LocalizedException(__('No packages for request'));
        }

        $this->_prepareShipmentRequest($request);

        return $this->_doShipmentRequest($request);
    }

    /**
     * {@inheritDoc}
     *
     * @param Request $request
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        $insured = (bool) $this->getConfigData('insured');
        $shipmentItems = $this->getShipmentItems($request);
        $order = $request->getOrderShipment()->getOrder();

        $weight = intval($request->getPackageWeight());

        $data = [
            'linea' => $this->getConfigData('line'),
            'id_contrato' => $this->getConfigData('contract'),
            'cod_rastreo' => $order->getIncrementId(),
            'fech_emi_vent' => (new \DateTime($order->getCreatedAt()))->format('d/m/Y'),
            'nro_o_compra' => $order->getIncrementId(),
            'cod_cliente' => (string) $order->getCustomerId(),
            'nom_cliente' => $request->getRecipientContactPersonName(),
            'nom_empresa' => (string) $request->getRecipientContactCompanyName(),
            'nro_telf' => $request->getRecipientContactPhoneNumber(),
            'nro_telf_mobil' => '',
            'correo_elec' => $request->getRecipientEmail(),
            'dir_entrega' => $request->getRecipientAddressStreet1(),
            'nro_via' => (string) $order->getShippingAddress()->getData('numero_exterior'),
            'nro_int' => (string) $order->getShippingAddress()->getData('numero_interior'),
            'nom_urb' => $request->getRecipientAddressCity(),
            'ubi_direc' => $request->getRecipientAddressPostalCode(),
            'ref_direc' => '',
            'peso_total' => $weight,
            'pieza_total' => count($request->getPackages()),
            'urgente' => (bool) $this->getConfigData('urgent') ? 'SI' : 'NO',
            'picking' => (bool) $this->getConfigData('picking') ? 'SI' : 'NO',
            'mecanizado' => (bool) $this->getConfigData('mecanizado') ? 'SI' : 'NO',
            'asegurado' => $insured ? 'SI' : 'NO',
            'monto_asegurado' => $insured ? $order->getGrandTotal() : '',
            'via_aereo' => self::METHOD_AEREO === $request->getShippingMethod() ? 'SI' : 'NO',
            'venta_seller' => (bool) $this->getConfigData('seller') ? 'SI' : 'NO',
            'sell_codigo' => (string) $this->getConfigData('seller_code'),
            'sell_nombre' => (string) $this->getConfigData('seller_name'),
            'sell_direcc' => (string) $this->getConfigData('seller_address'),
            'sell_ubigeo' => (string) $this->getConfigData('seller_ubigeo'),
            'productos' => $shipmentItems,
        ];

        $resultLabel = $this->getApiClient()->generateLabel($data);
        $labelData = [];

        if (isset($resultLabel['guia'])) {
            $labelData[] = [
                'tracking_number' => $resultLabel['guia'],
            ];
        }

        $response = new DataObject(['info' => $labelData]);

        if (empty($labelData)) {
            $response->setErrors($this->getApiClient()->getLastError());
        }

        return $response;
    }

    /**
     * Get tracking info.
     *
     * @param string $trackingNumber
     *
     * @return Result
     */
    protected function getTracking(string $trackingNumber): Result
    {
        $this->trackingResult = $this->_trackFactory->create();

        $data = [
            'guia' => $trackingNumber,
            'vp_linea' => $this->getConfigData('line'),
        ];

        $trackingInfo = $this->getApiClient()->getTrackingInfo($data);

        if (empty($trackingInfo)) {
            $this->appendTrackingError($trackingNumber, $this->getApiClient()->getLastError());
        } else {
            $this->parseTrackingResponse($trackingNumber, $trackingInfo);
        }

        return $this->trackingResult;
    }

    /**
     * Get quotes from api.
     *
     * @return array
     */
    private function getQuotes(): array
    {
        /** @var RateRequest $request */
        $request = $this->_rawRequest;

        $data = [
            'linea' => $this->getConfigData('line'),
            'id_orden' => $this->getConfigData('contract'),
            'origen' => $request->getPostcode(),
            'destino' => $request->getDestPostcode(),
            'peso' => $this->getConfigData('weight'),
            'alto' => $this->getConfigData('height'),
            'largo' => $this->getConfigData('length'),
            'ancho' => $this->getConfigData('width'),
            'tipo_empaque' => $this->getConfigData('package_type'),
            'seguro' => $request->getPackageValue(),
        ];

        $quotes = $this->getApiClient()->getQuotes($data);

        return $this->parseQuotes($quotes);
    }

    /**
     * Parse quotes.
     *
     * @param array $quotes
     *
     * @return array
     */
    private function parseQuotes(array $quotes): array
    {
        $quoteService = [];
        $insurance = [];
        $this->_logger->error("Urbano quotes to parse:\n" . print_r($quotes, true) . "\n");
        foreach ($quotes as $quote) {
            if (!isset($quote['id_servicio'])) {
                continue;
            }

            if (self::QUOTE_SERVICE_ID === $quote['id_servicio']) {
                $quoteService = $quote;

                continue;
            }

            if (self::QUOTE_INSURANCE_ID === $quote['id_servicio']) {
                $insurance = $quote;
            }
        }
        $this->_logger->error("Urbano \$quoteService:\n" . print_r($quoteService, true) . "\n");
        $this->_logger->error("Urbano \$insurance:\n" . print_r($insurance, true) . "\n");

        if (!$quoteService) {
            return [];
        }

        $quoteMethods = [];
        $insuranceCost = $insurance['valor_ennvio'] ?? 0;
        $igv = (float) $this->getConfigData('igv');

        // Split methods.
        if (!empty($quoteService['valor_ennvio']) && (float) $quoteService['valor_ennvio'] > 0) {
            $cost = (float) $quoteService['valor_ennvio'];

            if ($igv > 0) {
                $cost += $cost * ($igv / 100);
            }

            $quoteMethods[] = [
                'method' => self::METHOD_TERRESTRE,
                'cost' => $cost + $insuranceCost,
                'time' => $quoteService['time_envio'],
            ];
        }

        if (!empty($quoteService['valor_envio_aereo']) && (float) $quoteService['valor_envio_aereo'] > 0) {
            $cost = (float) $quoteService['valor_envio_aereo'];

            if ($igv > 0) {
                $cost += $cost * ($igv / 100);
            }

            $quoteMethods[] = [
                'method' => self::METHOD_AEREO,
                'cost' => $cost + $insuranceCost,
                'time' => $quoteService['time_aereo'],
            ];
        }

        return $quoteMethods;
    }

    /**
     * Shipment items.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getShipmentItems(Request $request): array
    {
        $insured = (bool) $this->getConfigData('insured');

        $items = [];

        /** @var Item $item */
        foreach ($request->getOrderShipment()->getAllItems() as $item) {
            $items[$item->getProductId()] = $item;
        }

        $shipmentItems = [];
        foreach ($request->getPackages() as $package) {
            $skus = [];
            $productName = [];
            $qty = 0;
            foreach ($package['items'] as $packageItem) {
                /** @var Item $item */
                $item = $items[$packageItem['product_id']];

                $skus[] = $item->getSku();
                $productName[] = $item->getName();
                $qty += (int) $packageItem['qty'];
            }

            $shipmentItems[] = [
                'cod_sku' => substr(implode(',', $skus), 0, 25),
                'descr_sku' => substr(implode(', ', $productName), 0, 100),
                'peso_sku' => $package['params']['weight'],
                'valor_sku' => $insured ? $package['params']['customs_value'] : '',
                'alto' => $package['params']['height'],
                'largo' => $package['params']['length'],
                'ancho' => $package['params']['width'],
            ];
        }

        return $shipmentItems;
    }

    /**
     * Append tracking error.
     *
     * @param string $trackingNumber
     * @param mixed $errorMessage
     *
     * @return void
     */
    private function appendTrackingError(string $trackingNumber, $errorMessage)
    {
        $error = $this->_trackErrorFactory->create();
        $error->setCarrier($this->_code);
        $error->setCarrierTitle($this->getConfigData('title'));
        $error->setTracking($trackingNumber);
        $error->setErrorMessage($errorMessage);

        $this->trackingResult->append($error);
    }

    /**
     * Parse tracking result.
     *
     * @param string $trackingNumber
     * @param array $trackingInfo
     *
     * @return void
     */
    private function parseTrackingResponse(string $trackingNumber, array $trackingInfo)
    {
        $tracking = $this->_trackStatusFactory->create();
        $tracking->setCarrier($this->_code);
        $tracking->setCarrierTitle($this->getConfigData('title'));
        $tracking->setTracking($trackingNumber);
        $tracking->addData($this->processTrackingDetails(current($trackingInfo)));

        $this->trackingResult->append($tracking);
    }

    /**
     * Process tracking details.
     *
     * @param array $trackingInfo
     *
     * @return array
     */
    private function processTrackingDetails(array $trackingInfo): array
    {
        $delivered = [];

        // Check delivered.
        foreach ($trackingInfo['movimientos'] as $movement) {
            if ('EN' !== $movement['chk']) {
                continue;
            }

            $delivered = $movement;
        }

        return [
            'status' => $trackingInfo['estado'],
            'signedby' => $delivered['apunts'] ?? null,
            'shipped_date' => $trackingInfo['hora_pickup'],
            'service' => $trackingInfo['servicio'],
            'weight' => $trackingInfo['peso'],
            'deliverydate' => $delivered['fecha'] ?? null,
            'deliverytime' => $delivered['hora'] ?? null,
            'progressdetail' => $this->processTrackDetails($trackingInfo['movimientos']),
        ];
    }

    /**
     * Tracking movements.
     *
     * @param array $movements
     *
     * @return array
     */
    private function processTrackDetails(array $movements): array
    {
        $result = [];

        foreach ($movements as $movement) {
            $date = Carbon::createFromFormat(
                'd/m/Y H:i',
                sprintf('%s %s', $movement['fecha'], $movement['hora'])
            );

            $result[] = [
                'status_code' => $movement['chk'],
                'activity' => sprintf('%s - %s', $movement['estado'], $movement['sub_estado']),
                'deliverydate' => $date->toDateString(),
                'deliverytime' => $date->toTimeString(),
                'deliverylocation' => $movement['apunts'],
            ];
        }

        return $result;
    }
}
