<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Controller\Adminhtml;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use WolfSellers\Consecutive\Model\SequentialRepository;

abstract class Sequential implements ActionInterface
{
    const ADMIN_RESOURCE = 'WolfSellers_Consecutive::top_level';

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var RedirectFactory
     */
    protected RedirectFactory $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;

    /**
     * @var SequentialRepository
     */
    protected SequentialRepository $sequentialRepository;

    /**
     * @param RequestInterface $request
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManage
     * @param SequentialRepository $sequentialRepository
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManage,
        SequentialRepository $sequentialRepository
    )
    {
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManage;
        $this->sequentialRepository = $sequentialRepository;
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('WolfSellers'), __('WolfSellers'))
            ->addBreadcrumb(__('Sequential'), __('Sequential'));

        return $resultPage;
    }
}

