<?php

namespace WolfSellers\ZipCode\Setup\Patch\Data;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallUbigeosData implements DataPatchInterface
{
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
        $fileName = $dir->getRelativePath($etcDirPath . '/peru.json');

        if ($dir->isExist($fileName)) {
            try {
                $content = $dir->readFile($fileName);

                $data = $this->json->unserialize($content);

                $tableName = $this->moduleDataSetup->getTable('wolfsellers_zipcode');

                $this->moduleDataSetup
                    ->getConnection()
                    ->insertMultiple($tableName, $data);
            } catch (\Exception $exception) {
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
