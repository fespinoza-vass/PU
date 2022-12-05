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

/**
 * Checkout Directory Data Processor Plugin.
 */
class DirectoryDataProcessorPlugin
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
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
    ) {

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

        return $result;
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
            ->distinct()
        ;

        $cities = $connection->fetchAll($select);

        array_unshift($cities, [
            'value' => '',
            'region_id' => '',
            'label' => '',
        ]);

        return $cities;
    }
}
