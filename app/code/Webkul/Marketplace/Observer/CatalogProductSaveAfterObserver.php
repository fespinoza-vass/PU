<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Marketplace\Model\ProductFactory as MpProductFactory;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory;

/**
 * Webkul Marketplace CatalogProductSaveAfterObserver Observer.
 */
class CatalogProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @var MpProductFactory
     */
    protected $mpProductFactory;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CollectionFactory                           $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param MpProductFactory                            $mpProductFactory
     * @param MpHelper                                    $mpHelper
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        MpProductFactory $mpProductFactory = null,
        MpHelper $mpHelper = null
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_date = $date;
        $this->messageManager = $messageManager;
        $this->mpProductFactory = $mpProductFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(MpProductFactory::class);
        $this->mpHelper = $mpHelper ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(MpHelper::class);
    }

    /**
     * Product save after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $product = $observer->getProduct();
            $assginSellerData = $product->getAssignSeller();
            $productId = $product->getId();
            $productRowId = $product->getRowId();
            $status = $product->getStatus();
            $sellerProductColl = $this->mpProductFactory->create()->getCollection()
                ->addFieldToFilter('mage_pro_row_id', $productRowId)
                ->addFieldToFilter('mageproduct_id', $productId)
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
            if ($sellerProductColl->getId()) {
                if ($status != $sellerProductColl->getStatus()) {
                    $sellerProductColl->setStatus($status)->save();
                }
            } else {
                $sellerProductFactory = $this->mpProductFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('mageproduct_id', $productId)
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem();
                if ($sellerProductFactory->getId()) {
                    $sellerId = $sellerProductFactory->getSellerId();
                    $sellerProduct = $this->mpProductFactory->create();
                    $sellerProduct->setMageproductId($productId);
                    $sellerProduct->setMageProRowId($productRowId);
                    $sellerProduct->setSellertId($sellerId);
                    $sellerProduct->setStatus($status);
                    $sellerProduct->save();
                } elseif (is_array($assginSellerData) &&
                    isset($assginSellerData['seller_id']) &&
                    $assginSellerData['seller_id'] != ''
                ) {
                    $sellerId = $assginSellerData['seller_id'];
                    $mpProductModel = $this->mpProductFactory->create();
                    $mpProductModel->setMageProRowId($productRowId);
                    $mpProductModel->setMageproductId($productId);
                    $mpProductModel->setSellerId($sellerId);
                    $mpProductModel->setStatus($product->getStatus());
                    $mpProductModel->setAdminassign(1);
                    $isApproved = 1;
                    if ($product->getStatus() == 2 && $this->mpHelper->getIsProductApproval()) {
                        $isApproved = 0;
                    }
                    $mpProductModel->setIsApproved($isApproved);
                    $mpProductModel->setCreatedAt($this->_date->gmtDate());
                    $mpProductModel->setUpdatedAt($this->_date->gmtDate());
                    $mpProductModel->save();
                }
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger(
                "Observer_CatalogProductSaveAfterObserver execute : ".$e->getMessage()
            );
            $this->messageManager->addError($e->getMessage());
        }
    }
}
