<?php

namespace WolfSellers\SkinCare\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface as productRepository;
use Magento\Customer\Model\Session as customerSession;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Store\Model\StoreManagerInterface as storeManager;


use WolfSellers\SkinCare\Helper\Constants;

use Psr\Log\LoggerInterface;


class CollectSimulatorDataAndSendEmail implements \Magento\Framework\Event\ObserverInterface
{
    protected productRepository $productRepository;
    protected customerSession $customerSession;
    protected priceHelper $priceHelper;
    protected storeManager $storeManager;

    protected LoggerInterface $logger;

    public function __construct(
        productRepository $productRepository,
        customerSession   $customerSession,
        priceHelper       $priceHelper,
        storeManager      $storeManager,
        LoggerInterface   $logger
    )
    {
        $this->productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->priceHelper = $priceHelper;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $answer = $observer->getData('answer');
            $form = $observer->getData('form');

            if ($form->getFormId() == '26' and $answer->getData('textinput-1663957503940')) {
                $this->logger->info(__METHOD__);
                $this->logger->info('-------------------- ANSWER --------------------');
                foreach ($answer->getData() as $key => $value) {
                    if (is_scalar($value) or is_string($value)) {
                        $this->logger->info('[' . $key . '] => ' . $value);
                    }
                }
                $this->logger->info('-------------------- FORM --------------------');
                $this->logger->info(print_r($form->getData(), true));

                $userEmail = $answer->getData('textinput-1663957503940');
                $this->sendVariablesByEmail($userEmail);
            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__);
            $this->logger->error($e->getMessage());
        }
    }

    private function sendVariablesByEmail(string $userEmail)
    {
        /**
         * Get session variables
         */
        $wrinklePercentage = $this->customerSession->getWrinklePercentage();
        $wrinkleProductIds = $this->customerSession->getWrinkleProductIds();

        $spotPercentage = $this->customerSession->getSpotPercentage();
        $spotProductIds = $this->customerSession->getSpotProductIds();

        $texturePercentage = $this->customerSession->getTexturePercentage();
        $textureProductIds = $this->customerSession->getTextureProductIds();

        $darkCirclePercentage = $this->customerSession->getDarkCirclePercentage();
        $darkCircleProductIds = $this->customerSession->getDarkCircleProductIds();

        /**
         * Start empty arrays
         */
        $result = [];
        $result[Constants::LINEAS_DE_EXPRESION] = [];
        $result[Constants::MANCHAS] = [];
        $result[Constants::TEXTURA] = [];
        $result[Constants::OJERAS] = [];
        $result['results'] = [];

        /**
         * Set percentages
         */
        $result['results'][Constants::LINEAS_DE_EXPRESION] = $wrinklePercentage;
        $result['results'][Constants::MANCHAS] = $spotPercentage;
        $result['results'][Constants::TEXTURA] = $texturePercentage;
        $result['results'][Constants::OJERAS] = $darkCirclePercentage;

        /**
         * Get UP TO 4 products for each type
         */
        $result[Constants::LINEAS_DE_EXPRESION] = $this->getFourProductsByType($wrinkleProductIds);
        $result[Constants::MANCHAS] = $this->getFourProductsByType($spotProductIds);
        $result[Constants::TEXTURA] = $this->getFourProductsByType($textureProductIds);
        $result[Constants::OJERAS] = $this->getFourProductsByType($darkCircleProductIds);

        //$this->xyz->sendEmail($userEmail, $result);

        /**
         * UNSET/empty sessionVariables
         */
        $this->customerSession->unsWrinklePercentage();
        $this->customerSession->unsWrinkleProductIds();

        $this->customerSession->unsSpotPercentage();
        $this->customerSession->unsSpotProdutIds();

        $this->customerSession->unsTexturePercentage();
        $this->customerSession->unsTextureProdutIds();

        $this->customerSession->unsDarkCirclePercentage();
        $this->customerSession->unsDarkCircleProdutIds();
    }

    /**
     * Get UP TO 4 products for each type
     *
     * @param $typeProductsArray
     * @return array
     */
    private function getFourProductsByType($typeProductsArray): array
    {
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
            $productInfo['urlImage'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            $productInfo['price'] = $this->priceHelper->currency($product->getPrice(), true, false);
            $productInfo['name'] = $product->getName();
            $productInfo['urlProduct'] = $product->getProductUrl();
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__);
            $this->logger->error($e->getMessage());
        }

        return $productInfo;
    }

}
