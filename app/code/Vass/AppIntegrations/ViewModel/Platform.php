<?php
/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_AppIntegrations
 * @author Vass Team
 */
declare(strict_types=1);

namespace Vass\AppIntegrations\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Platform implements ArgumentInterface
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly RequestInterface $request,
    ) {
    }

    /**
     * Get the platform
     *
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->request->getParam('mobile') ?? 'web';
    }
}
