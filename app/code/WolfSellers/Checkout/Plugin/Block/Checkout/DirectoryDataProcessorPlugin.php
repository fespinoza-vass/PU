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
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
/**
 * Checkout Directory Data Processor Plugin.
 */
class DirectoryDataProcessorPlugin
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;
    private Session $customerSession;
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param DirectoryDataProcessor $subject
     * @param $result
     * @param $jsLayout
     *
     * @return array
     */
    public function afterProcess(
        DirectoryDataProcessor $subject,
                               $result,
                               $jsLayout
    )
    {

        if (isset($result['components']['checkoutProvider']['dictionaries'])) {
            $result['components']['checkoutProvider']['dictionaries']['city_id'] = $this->getCities();
        }
        $session_CustomerID = $this->customerSession->getCustomerId();
        if ($this->validateDNI($session_CustomerID)){
            $dni = $this->getDNI($session_CustomerID);
            if ($dni){
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id']['value']=$dni;

            }
        }
        return $result;
    }

    public function getDNI($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $dni = $customer->getCustomAttribute('numero_de_identificacion')->getValue();
        if (!is_null($dni)){
            return $dni;
        }
        return false;
    }

    public function getDNI2($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $dni = $customer->getCustomAttribute('numero_de_identificacion')->getValue();
        if (!is_null($dni)){
            return $dni;
        }
        return false;
    }

    public function validateDNI($customerId){
        if ($this->customerSession->isLoggedIn()){
            $customer = $this->customerRepository->getById($customerId);
            $dni = $customer->getCustomAttribute('numero_de_identificacion')->getValue();
            if (!is_null($dni)){
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Get cities.
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
