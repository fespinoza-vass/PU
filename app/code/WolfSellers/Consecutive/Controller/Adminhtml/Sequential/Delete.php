<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Controller\Adminhtml\Sequential;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use WolfSellers\Consecutive\Controller\Adminhtml\Sequential;

class Delete extends Sequential
{

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->request->getParam('sequential_id');

        if ($id) {
            try {
                // init model and delete
                $sequential = $this->sequentialRepository->get($id);

                $this->sequentialRepository->delete($sequential);
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Sequential.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['sequential_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Sequential to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

