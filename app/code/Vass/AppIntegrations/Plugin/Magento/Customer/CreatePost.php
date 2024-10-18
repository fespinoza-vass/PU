<?php
/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_AppIntegrations
 * @author Vass Team
 */
declare(strict_types=1);

namespace Vass\AppIntegrations\Plugin\Magento\Customer;

use Magento\Customer\Controller\Account\CreatePost as ParentClass;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Store\Model\StoreManagerInterface;

class CreatePost
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param HttpContext $httpContext
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly HttpContext $httpContext,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Redirect after login
     *
     * @param ParentClass $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(
        ParentClass $subject,
        Redirect $result
    ): Redirect {
        $platform = $this->request->getParam('platform') ?? 'web';
        if (strtolower($platform) == 'web' || !$this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $result;
        }

        $url = $this->storeManager->getStore()->getBaseUrl() . 'customer/app/register?mobile=' . strtolower($platform);
        $result->setUrl($url);
        return $result;
    }
}
