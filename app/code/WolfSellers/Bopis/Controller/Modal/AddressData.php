<?php

namespace WolfSellers\Bopis\Controller\Modal;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Helper\Data;
use WolfSellers\Bopis\Model\BopisFactory;

/**
 *
 */
class AddressData implements HttpGetActionInterface {
    /**
     *
     */
    const SOURCE_PREFIX = 'online_';
    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * @var CountryFactory
     */
    private CountryFactory $countryFactory;

    /**
     * @var Session
     */
    private Session $checkoutSession;
    /**
     * @var BopisRepositoryInterface
     */
    private BopisRepositoryInterface $bopisRepository;
    /**
     * @var BopisFactory
     */
    private BopisFactory $bopisFactory;
    /**
     * @var Data
     */
    private Data $data;

    /**
     * @param JsonFactory $jsonFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param RequestInterface $request
     * @param CountryFactory $countryFactory
     * @param ProvinceFactory $provinceFactory
     */
    public function __construct(
        JsonFactory $jsonFactory,
        AddressRepositoryInterface $addressRepository,
        RequestInterface $request,
        CountryFactory $countryFactory,
        Session $checkoutSession,
        BopisRepositoryInterface $bopisRepository,
        BopisFactory $bopisFactory,
        Data $data
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->addressRepository = $addressRepository;
        $this->request = $request;
        $this->countryFactory = $countryFactory;
        $this->checkoutSession = $checkoutSession;
        $this->bopisRepository = $bopisRepository;
        $this->bopisFactory = $bopisFactory;
        $this->data = $data;
    }

    /**
     * @return Json
     */
    public function execute(): Json {
        $jsonResponse = $this->jsonFactory->create();

        $addressId = $this->request->getParam('address_id');

        try {
            $address = $this->addressRepository->getById($addressId);
            $country = $this->countryFactory->create()->loadByCode($address->getCountryId());

            $addressData = [
                'address_id' => $address->getId(),
                'address_type' => !is_null($address->getCustomAttribute('tipo_direccion')) ?
                    $address->getCustomAttribute('tipo_direccion')->getValue(): '',
                'informacion_adicional' => !is_null($address->getCustomAttribute('informacion_adicional')) ?
                    $address->getCustomAttribute('informacion_adicional')->getValue(): '',
                'country_id' => $address->getCountryId(),
                'country' => $country->getName(),
                'region_id' => $address->getRegionId(),
                'region' => $address->getRegion()->getRegion(),

            ];

            $street = $address->getStreet();

            $i = 1;
            foreach ($street as $item) {
                $addressData['street_' . $i] = $item;

                $i++;
            }
            $this->saveBopisData($addressData);

            $jsonResponse->setStatusHeader(200);
            $jsonResponse->setData($addressData);
        } catch (LocalizedException $e) {
            $jsonResponse->setStatusHeader(500);
        }

        return $jsonResponse;
    }

    /**
     * @param $addressData
     * @return void
     * @throws LocalizedException
     */
    protected function saveBopisData($addressData){
        try{
            $bopis = $this->bopisRepository->getByQuoteId($this->checkoutSession->getQuoteId());
        }catch (\Exception $exception){
            $bopis = $this->bopisFactory->create();
        }
        $currentCountry = $this->data->getCurrentCountry();
        $this->checkoutSession->setValidatedItems([]);
        $this->checkoutSession->setWasValidated(false);

        $bopis->setQuoteId($this->checkoutSession->getQuoteId());
        $bopis->setAddressObject(json_encode($addressData));
        $bopis->setType("delivery");
        $bopis->setAddressFormatted($this->formatAddress($addressData));
        $bopis->setStore(self::SOURCE_PREFIX . $currentCountry);
        $this->bopisRepository->save($bopis);
    }

    /**
     * @param $addressData
     * @return String
     */
    protected function formatAddress($addressData): String {
        return '<span class="title">Dirección de Envío</span><span class="address">' . trim($addressData['street_1'] . " " . ($addressData['street_2'] ?? "") . " " . ($addressData['street_3'] ?? "")). ", " . $addressData['province'] . " " . $addressData['region'] . " " . $addressData['country'] .'</span>';
    }
}
