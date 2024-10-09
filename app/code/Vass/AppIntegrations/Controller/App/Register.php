<?php
/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_AppIntegrations
 * @author Vass Team
 */
declare(strict_types=1);

namespace Vass\AppIntegrations\Controller\App;

use Magento\Customer\Model\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Register implements HttpGetActionInterface
{
    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param HttpContext $httpContext
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        private readonly PageFactory $resultPageFactory,
        private readonly HttpContext $httpContext,
        private readonly ResultFactory $resultFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Register Success'));
        return $resultPage;
    }
}
