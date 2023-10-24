<?php

namespace WolfSellers\Bopis\Controller\Ajax;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Checkout\Model\Session;

class FastDeliveryAvailable implements HttpGetActionInterface
{
    /** @var int  */
    const MINIMUM_SALABLE_QUANTITY = 2;

    /**
     * @param JsonFactory $jsonResultFactory
     * @param Session $_checkout
     * @param GetSalableQuantityDataBySku $salableQuantityDataBySku
     */
    public function __construct(
        protected JsonFactory                 $jsonResultFactory,
        protected Session                     $_checkout,
        protected GetSalableQuantityDataBySku $salableQuantityDataBySku
    )
    {
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function execute()
    {
        $items = $this->_checkout->getQuote()->getAllVisibleItems();

        $fastDeliveryAvailable = 1;

        foreach ($items as $item) {
            $response = $this->salableQuantityDataBySku->execute($item->getSku());
            $saleableQuatity = current($response)['qty'];
            if ($saleableQuatity < self::MINIMUM_SALABLE_QUANTITY) {
                $fastDeliveryAvailable = 0;
            }
        }

        $result = $this->jsonResultFactory->create();
        $data = ['available' => "$fastDeliveryAvailable"];
        $result->setData($data);
        return $result;
    }
}
