<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-07-15
 * Time: 16:14
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Model\Shipping;

use Magento\Backend\Model\Auth\Session;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Division;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Shipping\Model\Config;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Shipping\Model\Shipment\RequestFactory;
use Magento\Shipping\Model\Shipping\Labels;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\UserFactory;

/**
 * Shipping Labels Preference.
 */
class LabelsPreference extends Labels
{
    public const XML_PATH_SHIPPING_CONTACT_ID = 'shipping/origin/contact_id';

    /** @var UserFactory */
    private UserFactory $userFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $shippingConfig
     * @param StoreManagerInterface $storeManager
     * @param CarrierFactory $carrierFactory
     * @param ResultFactory $rateResultFactory
     * @param RequestFactory $shipmentRequestFactory
     * @param RegionFactory $regionFactory
     * @param Division $mathDivision
     * @param StockRegistryInterface $stockRegistry
     * @param Session $authSession
     * @param Request $request
     * @param UserFactory $userFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $shippingConfig,
        StoreManagerInterface $storeManager,
        CarrierFactory $carrierFactory,
        ResultFactory $rateResultFactory,
        RequestFactory $shipmentRequestFactory,
        RegionFactory $regionFactory,
        Division $mathDivision,
        StockRegistryInterface $stockRegistry,
        Session $authSession,
        Request $request,
        UserFactory $userFactory
    ) {
        parent::__construct($scopeConfig, $shippingConfig, $storeManager, $carrierFactory, $rateResultFactory,
            $shipmentRequestFactory, $regionFactory, $mathDivision, $stockRegistry, $authSession, $request);
        $this->userFactory = $userFactory;
    }

    /**
     * Prepare and do request to shipment.
     *
     * @param Shipment $orderShipment
     *
     * @return DataObject
     *
     * @throws LocalizedException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function requestToShipment(Shipment $orderShipment)
    {
        $order = $orderShipment->getOrder();

        $shippingMethod = $order->getShippingMethod(true);
        $shipmentStoreId = $orderShipment->getStoreId();
        $shipmentCarrier = $this->_carrierFactory->create($order->getShippingMethod(true)->getCarrierCode());
        $baseCurrencyCode = $this->_storeManager->getStore($shipmentStoreId)->getBaseCurrencyCode();

        if (!$shipmentCarrier) {
            throw new LocalizedException(__('The "%1" carrier is invalid. Verify and try again.', $shippingMethod->getCarrierCode()));
        }

        $shipperRegionCode = $this->_scopeConfig->getValue(
            Shipment::XML_PATH_STORE_REGION_ID,
            ScopeInterface::SCOPE_STORE,
            $shipmentStoreId
        );

        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = $this->_regionFactory->create()->load($shipperRegionCode)->getCode();
        }

        $originStreet1 = $this->_scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ADDRESS1,
            ScopeInterface::SCOPE_STORE,
            $shipmentStoreId
        );
        $storeInfo = new DataObject(
            (array) $this->_scopeConfig->getValue(
                'general/store_information',
                ScopeInterface::SCOPE_STORE,
                $shipmentStoreId
            )
        );

        if (!$storeInfo->getName()
            || !$storeInfo->getPhone()
            || !$originStreet1
            || !$this->_scopeConfig->getValue(
                Shipment::XML_PATH_STORE_CITY,
                ScopeInterface::SCOPE_STORE,
                $shipmentStoreId
            )
            || !$this->_scopeConfig->getValue(
                Shipment::XML_PATH_STORE_ZIP,
                ScopeInterface::SCOPE_STORE,
                $shipmentStoreId
            )
            || !$this->_scopeConfig->getValue(
                Shipment::XML_PATH_STORE_COUNTRY_ID,
                ScopeInterface::SCOPE_STORE,
                $shipmentStoreId
            )
        ) {
            throw new LocalizedException(__("Shipping labels can't be created. Verify that the store information and settings are complete and try again."));
        }

        $adminId = $this->_scopeConfig->getValue(
            self::XML_PATH_SHIPPING_CONTACT_ID,
            ScopeInterface::SCOPE_STORE,
            $shipmentStoreId
        );
        $admin = $this->userFactory->create()->load($adminId);

        /** @var $request Request */
        $request = $this->_shipmentRequestFactory->create();
        $request->setOrderShipment($orderShipment);
        $address = $order->getShippingAddress();

        $this->setShipperDetails($request, $admin, $storeInfo, $shipmentStoreId, $shipperRegionCode, $originStreet1);
        $this->setRecipientDetails($request, $address);

        $request->setShippingMethod($shippingMethod->getMethod());
        $request->setPackageWeight($order->getWeight());
        $request->setPackages($orderShipment->getPackages());
        $request->setBaseCurrencyCode($baseCurrencyCode);
        $request->setStoreId($shipmentStoreId);

        return $shipmentCarrier->requestToShipment($request);
    }
}
