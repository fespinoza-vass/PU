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

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment\Item;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Shipment\Request;
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

        if (!$quotes) {
            $this->_logger->error('Urbano: Not quoates available');
        }


        $result = $this->_rateFactory->create();
        $methods = $this->getAllowedMethods();

        // Append quotes to result
        foreach ($quotes as $quote) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($quote['method']);
            $method->setMethodTitle($methods[$quote['method']]);

            $method->setPrice($quote['cost']);
            $method->setCost($quote['cost']);

            $result->append($method);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function processAdditionalValidation(DataObject $request)
    {
        //Skip by item validation if there is no items in request
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
            $allowedMethods[$key] = $this->getConfigData(sprintf('%s_name', $key));
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

        /** @var Order $order */
        $order = $request->getOrderShipment()->getOrder();

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
            'peso_total' => round($request->getPackageWeight(), 2),
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
     * Get quotes from api.
     *
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
        foreach ($quotes as $quote) {
            if (self::QUOTE_SERVICE_ID === $quote['id_servicio']) {
                $quoteService = $quote;

                continue;
            }

            if (self::QUOTE_INSURANCE_ID === $quote['id_servicio']) {
                $insurance = $quote;
            }
        }

        if (!$quoteService) {
            return [];
        }

        $quoteMethods = [];
        $insuranceCost = $insurance['valor_ennvio'] ?? 0;

        // Split methods.
        if (!empty($quoteService['valor_ennvio'])) {
            $quoteMethods[] = [
                'method' => self::METHOD_TERRESTRE,
                'cost' => $quoteService['valor_ennvio'] + $insuranceCost,
                'time' => $quoteService['time_envio'],
            ];
        }

        if (!empty($quoteService['valor_envio_aereo'])) {
            $quoteMethods[] = [
                'method' => self::METHOD_AEREO,
                'cost' => $quoteService['valor_envio_aereo'] + $insuranceCost,
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
}
