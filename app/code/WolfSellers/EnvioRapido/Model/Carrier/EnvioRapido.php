<?php

namespace WolfSellers\EnvioRapido\Model\Carrier;

use Psr\Log\LoggerInterface;

use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku as SalableQtyBySku;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;


class EnvioRapido extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'envio_rapido';
    protected $_isFixed = true;
    protected ResultFactory $_rateResultFactory;
    protected MethodFactory $_rateMethodFactory;
    protected ProductRepository $productRepository;
    protected SalableQtyBySku $salableQuantityDataBySku;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory         $rateErrorFactory,
        LoggerInterface      $logger,
        ResultFactory        $rateResultFactory,
        MethodFactory        $rateMethodFactory,
        ProductRepository    $productRepository,
        SalableQtyBySku      $salableQuantityDataBySku,
        array                $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->productRepository = $productRepository;
        $this->salableQuantityDataBySku = $salableQuantityDataBySku;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        try {

            if (!$this->getConfigFlag('active')) {
                return false;
            }
            $this->_logger->info(__METHOD__);

            //Reglas de negocio
            $cumpleReglasEnvioRapido = false;
            $salableQtySelectedStock = -1;

            /** @var Magento\Quote\Model\Quote\Item $item */
            foreach ($request->getAllItems() as $item) {
                //$productId = $item->getProductId();
                //$this->productRepository->getById($productId);
                $salable = $this->salableQuantityDataBySku->execute($item->getSku());
                //[0] => [ 'stock_id' => 2, 'stock_name' => 'Perfumerias Unidas', 'qty' => 8000, 'manage_stock' => true ]
                $salableQtySelectedStock = $salable[0]['qty'];
                $this->_logger->info('salableQtySelectedStock: ' . $salableQtySelectedStock);
                if($salableQtySelectedStock > 1000){
                    $cumpleReglasEnvioRapido = true;
                }
            }

            $shippingPrice = $this->getConfigData('price');

            if ($shippingPrice !== false) {
                $this->_logger->info('Diferente de false');
                if ($cumpleReglasEnvioRapido) {
                    $this->_logger->info('Si cumple reglas');
                    $result = $this->_rateResultFactory->create();

                    $method = $this->_rateMethodFactory->create();

                    $method->setCarrier($this->_code);
                    $method->setCarrierTitle($this->getConfigData('title'));

                    $method->setMethod($this->_code);
                    $method->setMethodTitle($this->getConfigData('name'));

                    if ($request->getFreeShipping() === true) {
                        $shippingPrice = '0.00';
                    }

                    $method->setPrice($shippingPrice);
                    $method->setCost($shippingPrice);
                    $method->setPrice('99.9');
                    $method->setCost('99.9');

                    $result->append($method);
                    return $result;

                } else {
                    $this->_logger->info('NO cumple reglas');
                    $error = $this->_rateErrorFactory->create();
                    $error->setCarrier($this->_code);
                    $error->setCarrierTitle($this->getConfigData('title'));
                    $error->setErrorMessage($salableQtySelectedStock . ' ' . $this->getConfigData('specificerrmsg'));
                    return $error;
                }
            }
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($salableQtySelectedStock . $e->getMessage());
            return $error;
        }
    }

    /**
     * getAllowedMethods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
