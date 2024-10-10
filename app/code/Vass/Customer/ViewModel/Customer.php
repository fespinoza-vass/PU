<?php
/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_Customer
 * @author Vass Team
 */
declare(strict_types=1);

namespace Vass\Customer\ViewModel;

use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Customer implements ArgumentInterface
{
    /**
     * Constructor
     *
     * @param Session $customerSession
     */
    public function __construct(
        private readonly Session $customerSession,
    ) {
    }

    /**
     * Get Customer data
     *
     * @return CustomerModel|null
     */
    public function getCustomer(): ?CustomerModel
    {
        return $this->customerSession->getCustomer() ?? null;
    }
}
