<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-06
 * Time: 09:50
 */

declare(strict_types=1);

namespace WolfSellers\FastShipping\Controller\Estimate;

use Carbon\Carbon;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use WolfSellers\Urbano\Model\Carrier\Urbano;

/**
 * Estimate shipping.
 */
class Index implements HttpPostActionInterface
{
    /** @var Http */
    private RequestInterface $request;

    /** @var JsonFactory  */
    private JsonFactory $jsonFactory;

    /** @var CarrierFactory */
    private CarrierFactory $carrierFactory;

    /** @var StoreManagerInterface */
    private StoreManagerInterface $storeManager;

    /** @var RateRequestFactory */
    private RateRequestFactory $rateRequestFactory;

    /** @var ProductRepositoryInterface */
    private ProductRepositoryInterface $productRepository;

    /** @var ScopeConfigInterface */
    private ScopeConfigInterface $scopeConfig;

    /** @var TimezoneInterface  */
    private TimezoneInterface $timezone;

    /** @var Resolver */
    private Resolver $localeResolver;

    /** @var Session */
    private Session $customerSession;

    /**
     * @param RequestInterface $request
     * @param JsonFactory $jsonFactory
     * @param CarrierFactory $carrierFactory
     * @param StoreManagerInterface $storeManager
     * @param RateRequestFactory $rateRequestFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory,
        CarrierFactory $carrierFactory,
        StoreManagerInterface $storeManager,
        RateRequestFactory $rateRequestFactory,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        TimezoneInterface $timezone,
        Resolver $localeResolver,
        Session $customerSession
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->carrierFactory = $carrierFactory;
        $this->storeManager = $storeManager;
        $this->rateRequestFactory = $rateRequestFactory;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->timezone = $timezone;
        $this->localeResolver = $localeResolver;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();

        $rateRequest = $this->createRateRequest();
        $resultRates = $this->getCarrierRates($rateRequest);

        if (!$resultRates || !count($resultRates->getAllRates())) {
            return $resultJson->setData([
                'success' => false,
                'message' => __('Shipping days not available'),
            ]);
        }

        $shippingDays = [];
        foreach ($resultRates->getAllRates() as $rate) {
            [$days, $hours] = explode(' ', $rate->getTime());
            $shippingDays[] = (int) $days;
        }

        $minDays = min($shippingDays);
        $shippingDateFormat = $this->getShippingDateFormatted($minDays);

        $response = [
            'success' => true,
            'days' => $minDays,
            'dateFormat' => $shippingDateFormat,
        ];

        $this->storeData($response);

        return $resultJson->setData($response);
    }

    /**
     * Get Product.
     *
     * @return ProductInterface
     *
     * @throws NoSuchEntityException
     */
    private function getProduct(): ProductInterface
    {
        $productId = (int) $this->request->getPostValue('product_id');

        return $this->productRepository->getById($productId);
    }

    /**
     * Create Rate Request.
     *
     * @return RateRequest
     *
     * @throws NoSuchEntityException
     */
    private function createRateRequest(): RateRequest
    {
        $product = $this->getProduct();
        $storeId = $this->storeManager->getStore()->getId();
        $ubigeo = $this->getUbigeo();
        $origPostcode = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ZIP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $request = $this->rateRequestFactory->create();
        $request->setDestPostcode($ubigeo);
        $request->setPackageValue($product->getPrice());
        $request->setPostcode($origPostcode);

        return $request;
    }

    /**
     * Carrier rates.
     *
     * @param RateRequest $rateRequest
     *
     * @return Result|bool
     *
     * @throws NoSuchEntityException
     */
    private function getCarrierRates(RateRequest $rateRequest)
    {
        $carrier = $this->carrierFactory->create(Urbano::CODE, $this->storeManager->getStore()->getId());

        return $carrier->collectRates($rateRequest);
    }

    /**
     * Calculate shipping date.
     *
     * @param int $days
     *
     * @return string
     */
    private function getShippingDateFormatted(int $days): string
    {
        Carbon::setLocale($this->localeResolver->getLocale());
        $shippingDate = Carbon::today($this->timezone->getConfigTimezone());
        $shippingDate->addDays($days);

        return $shippingDate->translatedFormat('l, M j');
    }

    /**
     * Store Data in session.
     *
     * @param array $data
     *
     * @return void
     */
    private function storeData(array $data)
    {
        $productId = (int) $this->request->getPostValue('product_id');

        $this->customerSession->setFastShippingUbigeo($this->getUbigeo());
        $this->customerSession->setFastShippingEstimate([
            'dateFormat' => $data['dateFormat'],
            'days' => $data['days'],
            'productId' => $productId,
        ]);
    }

    /**
     * Get ubigeo from request.
     *
     * @return string
     */
    private function getUbigeo(): string
    {
        return $this->request->getPostValue('ubigeo', $this->request->getPostValue('town'));
    }
}
