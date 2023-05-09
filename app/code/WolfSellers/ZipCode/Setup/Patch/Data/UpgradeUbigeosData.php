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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Upgrade Ubigeos Data.
 */
class UpgradeUbigeosData implements DataPatchInterface
{
    private const ZIPCODE_LENGTH = 6;

    /**
     * @var Reader
     */
    private Reader $reader;

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @param Reader $reader
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Filesystem $filesystem
     * @param Json $json
     */
    public function __construct(
        Reader $reader,
        ModuleDataSetupInterface $moduleDataSetup,
        Filesystem $filesystem,
        Json $json
    )
    {
        $this->reader = $reader;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->filesystem = $filesystem;
        $this->json = $json;
    }

    /**
     * {@inheritDoc}
     */
    public function apply()
    {
        $etcDirPath = $this->reader->getModuleDir(Dir::MODULE_ETC_DIR, 'WolfSellers_ZipCode');

        $dir = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        $fileName = $dir->getRelativePath($etcDirPath . '/peru-v2.json');

        if ($dir->isExist($fileName)) {
            try {
                $content = $dir->readFile($fileName);

                $data = $this->json->unserialize($content);

                array_walk($data, function (&$item) {
                    $postcode = (string) $item['postcode'];

                    if (self::ZIPCODE_LENGTH === strlen($postcode)) {
                        return;
                    }

                    $item['postcode'] = str_pad($postcode, 6, '0', STR_PAD_LEFT);
                });

                $tableName = $this->moduleDataSetup->getTable('wolfsellers_zipcode');

                $this->moduleDataSetup
                    ->getConnection()
                    ->delete($tableName);

                $this->moduleDataSetup
                    ->getConnection()
                    ->insertMultiple($tableName, $data);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies(): array
    {
        return [
            UpdatePostcode::class
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
