<?php

namespace WolfSellers\BackendBopis\Controller\Adminhtml\DashboardBopis;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Check if user has enough privileges
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WolfSellers_BackendBopis::dashboard');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Ordenes del día'));
        $this->_view->renderLayout();
    }
}
