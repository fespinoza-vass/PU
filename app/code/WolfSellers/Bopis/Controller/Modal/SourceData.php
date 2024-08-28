<?php

namespace WolfSellers\Bopis\Controller\Modal;

use Exception;
use Klarna\Kp\Model\Quote;
use Magento\Checkout\Model\Session;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Helper\Data;
use WolfSellers\Bopis\Model\BopisFactory;

class SourceData implements HttpGetActionInterface {
    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var CountryFactory
     */
    private CountryFactory $countryFactory;

    /**
     * @var Data
     */
    private Data $data;
    private Session $checkoutSession;
    private BopisFactory $bopisFactory;
    private BopisRepositoryInterface $bopisRepository;
    private CartManagementInterface $quoteManagement;
    private GuestCartRepositoryInterface $guestCartRepository;
    private CartRepositoryInterface $cartRepository;

    /**
     * @param JsonFactory $jsonFactory
     * @param RequestInterface $request
     * @param CountryFactory $countryFactory
     * @param Data $data
     */
    public function __construct(
        JsonFactory $jsonFactory,
        RequestInterface $request,
        CountryFactory $countryFactory,
        Data $data,
        Session $checkoutSession,
        BopisRepositoryInterface $bopisRepository,
        BopisFactory $bopisFactory,
        CartManagementInterface $quoteManagement,
        GuestCartRepositoryInterface $guestCartRepository,
        CartRepositoryInterface $cartRepository
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->countryFactory = $countryFactory;
        $this->data = $data;
        $this->checkoutSession = $checkoutSession;
        $this->bopisFactory = $bopisFactory;
        $this->bopisRepository = $bopisRepository;
        $this->quoteManagement = $quoteManagement;
        $this->guestCartRepository = $guestCartRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @return Json
     */
    public function execute(): Json {
        $jsonResponse = $this->jsonFactory->create();

        $sourceCode = $this->request->getParam('source_code');

        $source = $this->data->getSource($sourceCode);

        $sourceData = is_null($source) ? [] : $source->getData();
        $this->formatStreet($sourceData);

        $country = $this->countryFactory->create()->loadByCode($sourceData['country_id']);

        $sourceData['country'] = $country->getName();
        $this->saveBopis($sourceData);

        $jsonResponse->setStatusHeader(200);
        $jsonResponse->setData($sourceData);

        return $jsonResponse;
    }

    private function saveBopis($sourceData){
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
        $this->checkoutSession->setValidatedItems([]);
        $this->checkoutSession->setWasValidated(false);

        $bopis->setQuoteId($quoteId);
        $bopis->setAddressObject(json_encode($sourceData));
        $bopis->setType("store-pickup");
        $bopis->setAddressFormatted($this->formatAddress($sourceData));
        $bopis->setStore($sourceData['source_code']);
        $this->bopisRepository->save($bopis);
    }

    protected function formatAddress($sourceData): String {
        return '<span class="title">Tienda seleccionada: ' . $sourceData['name'] . '</span>'.
            '<span class="title method">' . 'Recoger en tienda</span><span class="store">' .
            $sourceData['name'] . '</span><span class="address">' . $sourceData['store_street'] . '</span>';
    }

    protected function formatStreet(&$sourceData){
        $prefix = strtolower($sourceData['country_id']);
        $street = "";
        $street .= $sourceData['street'];
        if ($prefix == 'co'){
            $street .= " " . $sourceData['co_street2'];
            $street .= " " . $sourceData['co_street3'];
            $street .= " | " . $sourceData['co_additional_info'];
        }
        $street .= ", " . $sourceData['city'];
        $sourceData['store_street'] = $street;
    }
}
