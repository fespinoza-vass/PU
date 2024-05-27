<?php

namespace WolfSellers\ZipCode\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdatePostcode implements DataPatchInterface
{
    /**
     * @var array|int[]
     */
    private array $ubigeosUpdate = [
        70101,
        70102,
        70103,
        70104,
        70105,
        70106,
        70107,
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritDoc}
     */
    public function apply()
    {
        $tableName = $this->moduleDataSetup->getTable('wolfsellers_zipcode');
        $connection = $this->moduleDataSetup
            ->getConnection();

        foreach ($this->ubigeosUpdate as $ubigeo) {
            $connection->update(
                $tableName,
                ['postcode' => str_pad((string) $ubigeo, 6, '0', STR_PAD_LEFT)],
                ['postcode = ?' => $ubigeo]
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies(): array
    {
        return [
            InstallUbigeosData::class
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
