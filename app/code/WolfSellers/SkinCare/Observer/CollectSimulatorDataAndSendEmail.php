<?php

namespace WolfSellers\SkinCare\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface as productRepository;
use Magento\Customer\Model\Session as customerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface as storeManager;

use WolfSellers\SkinCare\Api\Data\SimulatorInterface;
use WolfSellers\SkinCare\Model\SimulatorRepository;
use WolfSellers\SkinCare\Model\Source\SkinCareDiagnostico;
use WolfSellers\SkinCare\Helper\Constants;



class CollectSimulatorDataAndSendEmail implements \Magento\Framework\Event\ObserverInterface
{
    const LINEAS_DE_EXPRESION = 'wrinkle';
    const MANCHAS = 'spot';
    const TEXTURA = 'texture';
    const OJERAS = 'dark_circle';
    protected productRepository $productRepository;
    protected customerSession $customerSession;
    protected priceHelper $priceHelper;
    protected storeManager $storeManager;

    protected SkinCareDiagnostico $skinCareDiagnostico;

    private SimulatorRepository $simulatorRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private Json $json;

    public function __construct(
        productRepository   $productRepository,
        customerSession     $customerSession,
        priceHelper         $priceHelper,
        storeManager        $storeManager,
        SkinCareDiagnostico $skinCareDiagnostico,
        Json $json,
        SimulatorRepository $simulatorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->priceHelper = $priceHelper;
        $this->storeManager = $storeManager;
        $this->skinCareDiagnostico = $skinCareDiagnostico;
        $this->simulatorRepository = $simulatorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->json = $json;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $answer = $observer->getData('answer');
        $form = $observer->getData('form');

        if ($form->getCode() == 'PU-Simulador' and $answer->getData('response_json')) {
            $json = $answer->getData('response_json');
            $array = json_decode($json, true);
            $userName = $array['textinput-name']['value'] . ' ' . $array['textinput-lastname']['value'];
            $userEmail = $array['textinput-email']['value'];
            $formId = $array['textinput-formid']['value'];
            $skinHealth = $array['textinput-skinhealth']['value'] ?? '';
            $this->sendVariablesByEmail($userEmail,$formId, $skinHealth, $userName);
        }
    }

    private function sendVariablesByEmail(string $userEmail, $formId, $skinHealth, $userName)
    {
        $simulador = $this->getSimulatorData($formId);

        /** @var $wrinkle SimulatorInterface */
        $wrinkle = current(array_filter($simulador, function ($item){
            return $item->getType() == self::LINEAS_DE_EXPRESION;
        }));

        /** @var $spot SimulatorInterface */
        $spot = current(array_filter($simulador, function ($item){
            return $item->getType() == self::MANCHAS;
        }));

        /** @var $texture SimulatorInterface */
        $texture = current(array_filter($simulador, function ($item){
            return $item->getType() == self::TEXTURA;
        }));

        /** @var $darkCircle SimulatorInterface */
        $darkCircle = current(array_filter($simulador, function ($item){
            return $item->getType() == self::OJERAS;
        }));

        /**
         * Set percentages
         */

        $skinHealth = 0;
        $division = 0;

        if(is_bool($wrinkle) === true){
            $result['results'][Constants::LINEAS_DE_EXPRESION] = '';
        }else{
            $result['results'][Constants::LINEAS_DE_EXPRESION] = $wrinkle->getPercentage();
            $skinHealth = $skinHealth + intval($wrinkle->getPercentage());
            $division = $division + 1;
        }
        if(is_bool($spot) === true){
            $result['results'][Constants::MANCHAS] = '';
        }else{
            $result['results'][Constants::MANCHAS] = $spot->getPercentage();
            $skinHealth = $skinHealth + intval($spot->getPercentage());
            $division = $division + 1;
        }
        if(is_bool($texture) === true){
            $result['results'][Constants::TEXTURA] = '';
        }else{
            $result['results'][Constants::TEXTURA] = $texture->getPercentage();
            $skinHealth = $skinHealth + intval($texture->getPercentage());
            $division = $division + 1;
        }
        if(is_bool($darkCircle) ===  true){
            $result['results'][Constants::OJERAS] = '';
        }else{
            $result['results'][Constants::OJERAS] = $darkCircle->getPercentage();
            $skinHealth = $skinHealth + intval($darkCircle->getPercentage());
            $division = $division + 1;
        }
        /*$result['results'][Constants::LINEAS_DE_EXPRESION] = is_bool($wrinkle) ? '' : $wrinkle->getPercentage();
        $result['results'][Constants::MANCHAS] = is_bool($spot) ? '' : $spot->getPercentage();
        $result['results'][Constants::TEXTURA] = is_bool($texture) ? '' : $texture->getPercentage();
        $result['results'][Constants::OJERAS] = is_bool($darkCircle) ? '' : $darkCircle->getPercentage();*/

        $result['results'][Constants::SALUD_DE_PIEL] = round($skinHealth / $division);


        /**
         * Get UP TO 4 products for each type
         */
        $result[Constants::LINEAS_DE_EXPRESION] = is_bool($wrinkle) ? '' : $this->getFourProductsByType($wrinkle->getProductIds());
        $result[Constants::MANCHAS] = is_bool($spot) ? '' : $this->getFourProductsByType($spot->getProductIds());
        $result[Constants::TEXTURA] = is_bool($texture) ? '' : $this->getFourProductsByType($texture->getProductIds());
        $result[Constants::OJERAS] = is_bool($darkCircle) ? '' : $this->getFourProductsByType($darkCircle->getProductIds());

        /**
         * Set Customer Information
         */
        $result['customerName'] = $userName;

        $this->skinCareDiagnostico->sendEmail($userEmail, $result);
        $this->deleteSimulatorData($simulador);
    }

    /**
     * Get UP TO 4 products for each type
     *
     * @param $typeProductsArray
     * @return array
     */
    private function getFourProductsByType($typeProductsArray): array
    {
        $typeProductsArray = $this->json->unserialize($typeProductsArray);
        $arrayWithProductsInfo = [];

        if (is_array($typeProductsArray)) {
            $iterator = 0;
            foreach ($typeProductsArray as $productId) {
                $iterator++;
                if ($iterator > 4) {
                    break;
                }
                $productInfo = $this->getProductInfoForEmail($productId);
                $arrayWithProductsInfo[] = $productInfo;
            }
        }

        return $arrayWithProductsInfo;
    }

    /**
     * Get product urlImage, price, name and urlProduct
     * @param string $productId
     * @return array
     */
    private function getProductInfoForEmail(string $productId): array
    {
        $productInfo = [];
        try {
            $store = $this->storeManager->getStore();
            $product = $this->productRepository->getById($productId);
            $productInfo['productId'] = $productId;
            $productInfo['urlImage'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            $productInfo['price'] = $this->priceHelper->currency($product->getPrice(), true, false);
            $productInfo['name'] = $product->getName();
            $productInfo['urlProduct'] = $product->getProductUrl();
        } catch (\Exception $e) {
        }

        return $productInfo;
    }

    private function getSimulatorData($formId){
        $search =$this->searchCriteriaBuilder
            ->addFilter("form_id", $formId)
            ->create();
        $items = $this->simulatorRepository->getList($search);
        return $items->getItems();
    }

    private function deleteSimulatorData($simulatorData){
        foreach ($simulatorData as $simulator){
            $this->simulatorRepository->delete($simulator);
        }
    }
}
