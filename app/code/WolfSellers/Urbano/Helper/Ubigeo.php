<?php

namespace WolfSellers\Urbano\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use WolfSellers\Urbano\Helper\Config as ConfigValue;
use Magento\Framework\UrlInterface;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;

class Ubigeo extends AbstractHelper
{


    private Config $configValue;
    private DirectoryList $directory;
    private File $fileManager;

    public function __construct(
        Context $context,
        ConfigValue $config,
        DirectoryList $directoryList,
        File $file
    )
    {
        parent::__construct($context);
        $this->configValue = $config;
        $this->directory = $directoryList;
        $this->fileManager = $file;
    }


    /**
     * Method that returns
     *
     * @param $field
     * @param $value
     * @return array
     */
    public function getDays($ubigeo)
    {
        try {
            $estimated = $this->getFileData($ubigeo);
            if ($estimated !== false) {
                $this->logData($estimated);
                $dataEstimated = explode(" - ", $estimated);
                return ["type" => $dataEstimated[0], "days" => $dataEstimated[1]];
            } else {
                $this->logData('Ubigeo Data not Found', 'error');
                return ["days" => $this->configValue->getDefaultEstimated()];
            }
        } catch (\Exception $e) {
            $this->logData($e->getMessage(), 'error');
            return ["days" => $this->configValue->getDefaultEstimated()];
        }
    }


    private function getFileData($ubigeo)
    {
        $fileName = $this->_getConfigValue();
        $filePath = $this->getFilePath('/delivery_time/'.$fileName);
        if ($this->fileManager->isExists($filePath)){
            /**  Load $inputFileName to a Spreadsheet Object  **/
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


    private function getReaderType($fileName)
    {
        $explodedName = explode(".",$fileName);
        $extension = $explodedName[sizeof($explodedName)-1];
        if ($extension == 'xlsx') {
            return 'Xlsx';
        } elseif ($extension == 'xls'){
            return 'Xls';
        }
        return 'Xlsx';
    }
    private function getFilePath($file)
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
        $logger->info("Currency Code");
    }

    /**
     * Method to log info
     *
     * @param $text
     * @param $type
     * @return void
     */
    private function logData($text, $type = 'info')
    {
        $logger = $this->getLogger();
        if ($type == 'info') {
            $logger->info($text);
        } elseif ($type == 'alert'){
            $logger->alert($text);
        } elseif ($type == 'warn'){
            $logger->warn($text);
        } elseif ($type == 'crit'){
            $logger->crit($text);
        } elseif ($type == 'debug') {
            $logger->debug($text);
        } elseif ($type == 'error') {
            $logger->err($text);
        }
    }
}
