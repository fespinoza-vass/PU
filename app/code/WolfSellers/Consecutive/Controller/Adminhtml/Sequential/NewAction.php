<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Controller\Adminhtml\Sequential;

use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use WolfSellers\Consecutive\Controller\Adminhtml\Sequential;
use WolfSellers\Consecutive\Model\SequentialRepository;

class NewAction extends Sequential
{
    /**
     * @var ForwardFactory
     */
    protected ForwardFactory $resultForwardFactory;

    /**
     * @param RequestInterface $request
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManage
     * @param SequentialRepository $sequentialRepository
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManage,
        SequentialRepository $sequentialRepository,
        ForwardFactory $resultForwardFactory
    )
    {
        parent::__construct($request, $resultRedirectFactory, $messageManage, $sequentialRepository);

        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * New action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}

