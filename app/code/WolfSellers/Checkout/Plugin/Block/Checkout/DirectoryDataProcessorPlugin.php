<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-03-30
 * Time: 22:51
 */

declare(strict_types=1);

namespace WolfSellers\Checkout\Plugin\Block\Checkout;

use Magento\Checkout\Block\Checkout\DirectoryDataProcessor;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Checkout Directory Data Processor Plugin.
 */
class DirectoryDataProcessorPlugin
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var Session
     */
    private CustomerSession $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @param ResourceConnection $resourceConnection
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        ResourceConnection          $resourceConnection,
        CustomerSession             $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param DirectoryDataProcessor $subject
     * @param $result
     * @param $jsLayout
     * @return array
     */
    public function afterProcess(
        DirectoryDataProcessor $subject,
        $result,
        $jsLayout
    ) {
        if (isset($result['components']['checkoutProvider']['dictionaries'])) {
            $result['components']['checkoutProvider']['dictionaries']['city_id'] = $this->getCities();
        }

        if ($this->customerSession->isLoggedIn()) {
            $session_CustomerID = $this->customerSession->getCustomerId();
            $dni = $this->getDNI($session_CustomerID);

            if ($dni) {
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id']['value'] = $dni;

            }
        }
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id']["validation"]
            ["required-entry"] = true;

        return $result;
    }

    /**
     * @param $customerId
     * @return false|mixed
     */
    public function getDNI($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException|LocalizedException $e) {
            return false;
        }

        if ($customer->getCustomAttribute('numero_de_identificacion') === null) {
            return false;
        }

        $dni = $customer->getCustomAttribute('numero_de_identificacion')->getValue();
        if ($dni !== null) {
            return $dni;
        }

        return false;
    }

    /**
     * @param $customerId
     * @return false|mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getDNI2($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $dni = $customer->getCustomAttribute('numero_de_identificacion')->getValue();
        if ($dni != null){
            return $dni;
        }
        return false;
    }

    /**
     * @param $customerId
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateDNI($customerId)
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerRepository->getById($customerId);
            $dni = $customer->getCustomAttribute('numero_de_identificacion')->getValue();

            return ($dni !== null);
        }

        return false;
    }

    /**
     * Get city
     *
     * @return array
     */
    private function getCities(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('wolfsellers_zipcode');
        $cols = [
            'value' => 'ciudad',
            'label' => 'ciudad',
            'region_id'
        ];
        $select = $connection->select()
            ->from($tableName, $cols)
            ->order('ciudad ASC')
            ->distinct();

        $cities = $connection->fetchAll($select);

        array_unshift($cities, [
            'value' => '',
            'region_id' => '',
            'label' => '',
        ]);

        return $cities;
    }
}
