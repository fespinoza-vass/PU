<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Controller\Adminhtml\DireccionesTiendas;

class Delete extends \WolfSellers\DireccionesTiendas\Controller\Adminhtml\DireccionesTiendas
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('direccionestiendas_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\WolfSellers\DireccionesTiendas\Model\DireccionesTiendas::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Direccionestiendas.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['direccionestiendas_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Direccionestiendas to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

