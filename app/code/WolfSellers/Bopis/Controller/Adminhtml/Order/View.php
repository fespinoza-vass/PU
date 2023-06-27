<?php


namespace WolfSellers\Bopis\Controller\Adminhtml\Order;


class View  extends \Magento\Sales\Controller\Adminhtml\Order
{
    protected $resultPageFactory = false;
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';

    public function execute()
    {
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order) {
            try {
                $resultPage = $this->_initAction();
                $resultPage->getConfig()->getTitle()->prepend(__('Orden'));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('Exception occurred during order load'));
                return $this->_redirect($this->_redirect->getRefererUrl());
            }
            return $resultPage;
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        return $this->resultPageFactory->create();
    }

}
