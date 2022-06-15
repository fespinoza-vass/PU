<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-06-14
 * Time: 16:10
 */

declare(strict_types=1);

namespace WolfSellers\ZipCode\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Upgrade Ubigeos.
 */
class UpgradeData implements UpgradeDataInterface
{
    /** @var array|int[] */
    private array $ubigeosUpdate = [
        70101,
        70102,
        70103,
        70104,
        70105,
        70106,
        70107,
    ];

    /** @var ResourceConnection */
    private ResourceConnection $resource;

    /**
     * Constructor.
     *
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $tableName = $this->resource->getTableName('wolfsellers_zipcode');
            $connection = $this->resource->getConnection();

            foreach ($this->ubigeosUpdate as $ubigeo) {
                $connection->update(
                    $tableName,
                    ['postcode' => str_pad((string) $ubigeo, 6, '0', STR_PAD_LEFT)],
                    ['postcode = ?' => $ubigeo]
                );
            }
        }
    }
}
