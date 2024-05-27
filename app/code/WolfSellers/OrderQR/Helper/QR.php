<?php

namespace WolfSellers\OrderQR\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Backend\Helper\Data as BackendHelper;

/**
 *
 */
class QR extends AbstractHelper
{
    /**
     *
     */
    CONST URI_ORDER_VIEW_CONTROLLER = "GestorPU/order/view/order_id/";
    /**
     *
     */
    CONST XML_PATH_QR_ACTIVE = "bopis/qrcode_configuration/is_active";
    /**
     *
     */
    CONST QR_SIZE = "bopis/qrcode_configuration/qr_size";

    /** @var BackendHelper */
    protected $_backendHelper;
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var Filesystem
     */
    private $_fileSystem;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $fileSystem
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Filesystem $fileSystem,
        BackendHelper $backendHelper

    ) {
        $this->_backendHelper = $backendHelper;
        $this->_fileSystem = $fileSystem;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $incrementId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOrderAdminURLById($incrementId){
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $adminuri = $this->_backendHelper->getAreaFrontName();

        return $baseUrl . $adminuri . "/". self::URI_ORDER_VIEW_CONTROLLER . $incrementId ;
    }

    /**
     * @param $incrementId
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateQR($incrementId){

        $storeId = $this->_storeManager->getStore()->getId();
        $isActive = $this->getConfigValue(self::XML_PATH_QR_ACTIVE,$storeId);

        if(!$isActive){
            return;
        }

        $url =  $this->getOrderAdminURLById($incrementId);
        $qrImageSize = $this->getConfigValue(self::QR_SIZE,$storeId);



        $var = $this->_fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        $path = $var->getAbsolutePath().'qrcodes/';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }


        $renderer = new ImageRenderer(
            new RendererStyle($qrImageSize),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $writer->writeFile($url, $path. $incrementId. '.png');

        return $incrementId.".png";
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $incrementId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getURLQRImage($incrementId){
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        return $baseUrl."media/qrcodes/".$incrementId.".png";
    }
}
