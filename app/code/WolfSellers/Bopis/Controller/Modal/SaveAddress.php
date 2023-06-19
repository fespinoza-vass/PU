<?php

namespace WolfSellers\Bopis\Controller\Modal;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Helper\Data;
use WolfSellers\Bopis\Model\BopisFactory;

class SaveAddress implements HttpPostActionInterface
{
    const SOURCE_PREFIX = 'online_';
    private RequestInterface $request;
    private BopisRepositoryInterface $bopisRepository;
    private JsonFactory $jsonFactory;
    private Session $checkoutSession;
    private BopisFactory $bopisFactory;
    private CustomerSession $customerSession;
    private AddressInterfaceFactory $addressFactory;
    private AddressRepositoryInterface $addressRepository;
    private RegionInterfaceFactory $regionFactory;
    private CartManagementInterface $quoteManagement;
    private Data $data;

    public function __construct(
        RequestInterface           $request,
        BopisRepositoryInterface   $bopisRepository,
        JsonFactory                $jsonFactory,
        Session                    $checkoutSession,
        BopisFactory               $bopisFactory,
        CustomerSession            $customerSession,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory    $addressFactory,
        RegionInterfaceFactory     $regionFactory,
        CartManagementInterface    $quoteManagement,
        Data $data
    )
    {
        $this->request = $request;
        $this->bopisRepository = $bopisRepository;
        $this->jsonFactory = $jsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->bopisFactory = $bopisFactory;
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->addressRepository = $addressRepository;
        $this->regionFactory = $regionFactory;
        $this->quoteManagement = $quoteManagement;
        $this->data = $data;
    }

    public function execute()
    {
        $jsonResponse = $this->jsonFactory->create();
        $quoteId = $this->checkoutSession->getQuoteId();
        if ($quoteId == null){
            $quoteId = $this->quoteManagement->createEmptyCart();
            $this->checkoutSession->setQuoteId($quoteId);
        }
        try{
            $bopis = $this->bopisRepository->getByQuoteId($quoteId);
        }catch (Exception $exception){
            $bopis = $this->bopisFactory->create();
        }
        try{
            $currentCountry = $this->data->getCurrentCountry();
            $this->checkoutSession->setValidatedItems([]);
            $this->checkoutSession->setWasValidated(false);

            $bopis->setQuoteId($quoteId);
            $bopis->setAddressObject($this->request->getParam("address_data"));
            $bopis->setType($this->request->getParam("type"));
            $bopis->setAddressFormatted($this->addressFormatted($this->request->getParam("address_formatted")));
            $bopis->setStore(self::SOURCE_PREFIX . $currentCountry);
            $this->bopisRepository->save($bopis);

            /**
            if ($this->customerSession->isLoggedIn()){
                $this->saveAddress($this->request->getParam("address_data"));
            }
            */

            $jsonResponse->setStatusHeader(200);
            $jsonResponse->setData(["error" => false, "message" => "Direccion salvada"]);
        }catch (Exception $e){
            $jsonResponse->setStatusHeader(500);
            $jsonResponse->setData(["error" => true, "message" => $e->getMessage()]);
        }
        return $jsonResponse;
    }

    /**
     * @throws LocalizedException
     */
    private function saveAddress($addressObject){
        $addressObject = json_decode($addressObject, true);
        $address = $this->addressFactory->create();
        $street = [];
        for ($i = 1 ; $i < sizeof($addressObject) ; $i++){
            if (isset($addressObject['street_' . $i])){
                $street[] = $addressObject['street_' . $i];
            }
        }
        $address->setFirstname($addressObject['firstname']);
        $address->setLastname($addressObject['lastname']);
        $address->setTelephone($addressObject['telephone']);
        $address->setCustomerId($this->customerSession->getId());
        $address->setStreet($street);
        $address->setCountryId($addressObject['country_id']);
        $address->setRegionId($addressObject['region_id']);
        $address->setCity($addressObject['country_id']);
        $region = $this->regionFactory->create()
            ->setRegion($addressObject['region_id'])
            ->setRegionId($addressObject['region']);
        $address->setRegion($region);
        $address->setCustomAttribute("informacion_adicional", $addressObject['informacion_adicional']??'');
        $address->setCustomAttribute("identificacion", $addressObject['identificacion']??'');
        $address->setCustomAttribute("province_id", $addressObject['province_id']??'');
        $address->setCustomAttribute("district_id", $addressObject['province_id']??'');
        $address->setCustomAttribute("tipo_direccion", $addressObject['identificacion']??'');
        $this->addressRepository->save($address);
    }

    private function addressFormatted($address){
        return '<span class="title">Dirección de Envío</span><span class="address">' . $address .'</span>';
    }
}
