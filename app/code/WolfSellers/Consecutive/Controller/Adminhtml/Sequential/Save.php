<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Controller\Adminhtml\Sequential;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use WolfSellers\Consecutive\Controller\Adminhtml\Sequential;
use WolfSellers\Consecutive\Model\ConsecutiveRepository;
use WolfSellers\Consecutive\Model\Data\SequentialFactory;
use WolfSellers\Consecutive\Model\SequentialRepository;

class Save extends Sequential
{
    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var SequentialFactory
     */
    protected SequentialFactory $sequentialFactory;

    /**
     * @param RequestInterface $request
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManage
     * @param ConsecutiveRepository $sequentialRepository
     * @param DataPersistorInterface $dataPersistor
     * @param SequentialFactory $sequentialFactory
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManage,
        SequentialRepository $sequentialRepository,
        DataPersistorInterface $dataPersistor,
        SequentialFactory $sequentialFactory
    )
    {
        parent::__construct($request, $resultRedirectFactory, $messageManage, $sequentialRepository);

        $this->dataPersistor = $dataPersistor;
        $this->sequentialFactory = $sequentialFactory;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->request->getParams();

        if ($data) {
            $id = $this->request->getParam('sequential_id');

            try {
                $sequential = ($id) ? $this->sequentialRepository->get($id) : $this->sequentialFactory->create();

                if (!$sequential->getSequentialId() && $id) {
                    $this->messageManager->addErrorMessage(__('This Sequential no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }

                foreach ($data as $key => $value) {
                    $sequential->setData($key, $value);
                }

                $this->sequentialRepository->save($sequential);
                $this->messageManager->addSuccessMessage(__('You saved the Sequential.'));
                $this->dataPersistor->clear('wolfsellers_consecutive_sequential');

                if ($this->request->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['sequential_id' => $id]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Sequential.'));
            }

            $this->dataPersistor->set('wolfsellers_consecutive_sequential', $data);

            return $resultRedirect->setPath('*/*/edit', [
                'sequential_id' => $this->request->getParam('sequential_id')
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}

