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

use Cloudstek\PhpLaff\Packer;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
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

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates() || !$request->getDestPostcode()) {
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
     * {@inheritDoc}
     */
    public function getAllowedMethods()
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

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        // TODO: Implement _doShipmentRequest() method.
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

        $container = $this->getContainerDimensions();

        $data = [
            'linea' => $this->getConfigData('line'),
            'id_orden' => $this->getConfigData('line'),
            'origen' => $this->getConfigData('origin'),
            'destino' => $request->getDestPostcode(),
            'peso' => $request->getPackageWeight(),
            'alto' => $container['height'],
            'largo' => $container['length'],
            'ancho' => $container['width'],
            'tipo_empaque' => $this->getConfigData('package_type'),
            'seguro' => $request->getPackageValue(),
        ];

        $quotes = $this->getApiClient()->getQuotes($data);

        return $this->parseQuotes($quotes);
    }

    /**
     * Get Container dimensions.
     *
     * @return array|int[]
     */
    private function getContainerDimensions(): array
    {
        /** @var RateRequest $request */
        $request = $this->_rawRequest;

        $parcel = [];

        /** @var $item Item */
        foreach ($this->getAllItems($request) as $item) {
            /*$product = $item->getProduct();

            $container = [
                'length' => (float) $product->getLarge(),
                'width' => (float) $product->getWidth(),
                'height' => (float) $product->getHeight(),
            ];*/

            $container = [
                'length' => 1,
                'width' => 1,
                'height' => 1,
            ];

            $ordered = (int) $item->getQty();

            do {
                $parcel[] = $container;

                --$ordered;
            } while ($ordered > 0);
        }

        $laff = new Packer($parcel);
        $laff->pack();

        return $laff->get_container_dimensions();
    }

    private function parseQuotes(array $quotes)
    {
        $quoteService = [];
        $insurance = [];
        foreach ($quotes as $quote) {
            if (self::QUOTE_SERVICE_ID === $quote['id_servicio']) {
                $quoteService = $quote;
                break;
            }

            if (self::QUOTE_INSURANCE_ID === $quote['id_servicio']) {
                $insurance = $quote;
                break;
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
}
