<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-10-03
 * Time: 18:05
 */

declare(strict_types=1);

namespace WolfSellers\ZipCode\Setup\Patch\Data;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Upgrade Ubigeos Data.
 */
class UpgradeUbigeos02Data implements DataPatchInterface
{
    private const ZIPCODE_LENGTH = 6;

    /** @var Reader */
    private Reader $directoryList;

    /** @var ModuleDataSetupInterface */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Reader $directoryList
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Reader $directoryList
    ) {
        $this->directoryList = $directoryList;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritDoc}
     */
    public function apply()
    {
        $filesPath = $this->directoryList->getModuleDir('', 'WolfSellers_ZipCode');
        $filesPath .= '/Files/peru-v2.json';

        $data = json_decode(file_get_contents($filesPath), true);
        array_walk($data, function (&$item) {
            $postcode = (string) $item['postcode'];

            if (self::ZIPCODE_LENGTH === strlen($postcode)) {
                return;
            }

            $item['postcode'] = str_pad($postcode, 6, '0', STR_PAD_LEFT);
        });

        $zipcodeTable = $this->moduleDataSetup->getTable('wolfsellers_zipcode');
        $this->moduleDataSetup->getConnection()->delete($zipcodeTable);
        $this->moduleDataSetup->getConnection()->insertMultiple($zipcodeTable, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
