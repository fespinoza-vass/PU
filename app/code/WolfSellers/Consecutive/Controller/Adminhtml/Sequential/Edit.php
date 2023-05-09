<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Controller\Adminhtml\Sequential;

use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use WolfSellers\Consecutive\Controller\Adminhtml\Sequential;
use WolfSellers\Consecutive\Model\SequentialRepository;

class Edit extends Sequential
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @param RequestInterface $request
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManage
     * @param SequentialRepository $sequentialRepository
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManage,
        SequentialRepository $sequentialRepository,
        PageFactory $resultPageFactory,
        Registry $registry
    )
    {
        parent::__construct($request, $resultRedirectFactory, $messageManage, $sequentialRepository);

        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
    }

    /**
     * @return Redirect|ResultInterface|ResponseInterface|Page
     * @throws LocalizedException
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->request->getParam('sequential_id');

        // 2. Initial checking
        if ($id) {
            $consecutive = $this->sequentialRepository->get($id);
            if (!$consecutive->getSequentialId()) {
                $this->messageManager->addErrorMessage(__('This Sequential no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }

            //@TODO - Remove registry
            $this->registry->register('wolfsellers_consecutive_sequential', $consecutive);
        }

        $label = $id ? __('Edit Sequential') : __('New Sequential');

        // 3. Build edit form
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb($label, $label);

        $resultPage->getConfig()->getTitle()->prepend(__('Sequentials'));
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Sequential %1', $id) : __('New Sequential')
        );

        $this->initPage($resultPage);

        return $resultPage;
    }
}

