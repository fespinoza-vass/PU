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
namespace Webkul\Marketplace\Controller\Wysiwyg\Images;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

class Index extends \Webkul\Marketplace\Controller\Wysiwyg\Images
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($context, $registry);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        try {
            $this->_objectManager->get(\Magento\Cms\Helper\Wysiwyg\Images::class)->getCurrentPath();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $this->_initAction();
        $resultLayoutHandle = $this->resultLayoutFactory->create();
        $resultLayoutHandle->addHandle('overlay_popup');
        $wysiwgBlock = $resultLayoutHandle->getLayout()->getBlock('wysiwyg_images.js');
        if ($wysiwgBlock) {
            $wysiwgBlock->setStoreId($storeId);
        }
        return $resultLayoutHandle;
    }
}