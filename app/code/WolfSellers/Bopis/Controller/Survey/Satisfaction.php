<?php

namespace WolfSellers\Bopis\Controller\Survey;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Satisfaction extends Action
{
    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context                    $context,
        protected RequestInterface $request,
        protected PageFactory      $resultPageFactory
    )
    {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
