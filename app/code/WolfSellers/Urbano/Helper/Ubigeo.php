<?php

namespace WolfSellers\Urbano\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use WolfSellers\Urbano\Helper\Config as ConfigValue;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\FileSystemException;
class Ubigeo extends AbstractHelper
{


    /** @var Config $configValue */
    private Config $configValue;
    /** @var DirectoryList $directory */
    private DirectoryList $directory;
    /** @var File $fileManager */
    private File $fileManager;

    /**
     * Constructor to UbigeoHelper
     *
     * @param Context $context
     * @param Config $config
     * @param DirectoryList $directoryList
     * @param File $file
     */
    public function __construct(
        Context $context,
        ConfigValue $config,
        DirectoryList $directoryList,
        File $file
    ) {
        parent::__construct($context);
        $this->configValue = $config;
        $this->directory = $directoryList;
        $this->fileManager = $file;
    }

    /**
     * Method that returns
     *
     * @param mixed $ubigeo
     * @return array
     */
    public function getDays($ubigeo): array
    {
        try {
            $estimated = $this->getFileData($ubigeo);
            if ($estimated !== false) {
                $this->logData($estimated);
                return ["data" => $estimated];
            } else {
                $this->logData('Ubigeo Data not Found', 'error');
                return ["data" => $this->configValue->getDefaultEstimated()];
            }
        } catch (\Exception $e) {
            $this->logData($e->getMessage(), 'error');
            return ["data" => $this->configValue->getDefaultEstimated()];
        }
    }

    /**
     * Method that will catch the info filtering by ubigeo
     *
     * @param mixed $ubigeo
     * @return false|mixed
     * @throws FileSystemException
     */
    private function getFileData($ubigeo)
    {
        $fileName = $this->_getConfigValue();
        $filePath = $this->getFilePath('/delivery_time/'.$fileName);
        if ($this->fileManager->isExists($filePath)) {
            /*Load $inputFileName to a Spreadsheet Object*/
            $spreadsheet = IOFactory::load($filePath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $leatTimePuRow = '';
            $ubigeoRow = '';
            foreach ($sheetData as $row => $values) {
                foreach ($values as $column => $_value) {
                    if (str_contains($_value, 'LEAT TIME')) {
                        $leatTimePuRow = $column;
                    }
                    if ($_value == 'UBIGEO') {
                        $ubigeoRow = $column;
                    }
                }
                break;
            }
            foreach ($sheetData as $row => $values) {
                foreach ($values as $column => $_value) {
                    if ($ubigeoRow == $column && $_value == $ubigeo) {
                        return $values[$leatTimePuRow];
                    }
                }
            }

        }
        return false;
    }

    /**
     * Function to get the full path to get the file
     *
     * @param mixed $file
     * @return string
     * @throws FileSystemException
     */
    private function getFilePath($file): string
    {
        return $this->directory->getPath(DirectoryList::MEDIA).$file;
    }
    /**
     * This method will return de config value for estimated delivery
     *
     * @return mixed
     */
    protected function _getConfigValue()
    {
        return $this->configValue->getEstimateDelivery();
    }

    /**
     * Method to create a logger file
     *
     * @return \Laminas\Log\Logger
     */
    private function getLogger()
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/'.date('Ymd').'estimated_time_ubigeo.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        return $logger;
    }

    /**
     * Method to log info
     *
     * @param mixed $text
     * @param mixed $type
     * @return void
     */
    private function logData($text, $type = 'info')
    {
        $logger = $this->getLogger();
        if ($type == 'info') {
            $logger->info($text);
        } elseif ($type == 'alert') {
            $logger->alert($text);
        } elseif ($type == 'warn') {
            $logger->warn($text);
        } elseif ($type == 'crit') {
            $logger->crit($text);
        } elseif ($type == 'debug') {
            $logger->debug($text);
        } elseif ($type == 'error') {
            $logger->err($text);
        }
    }
}
