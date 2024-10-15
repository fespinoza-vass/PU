<?php
/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_Checkout
 * @author Vass Team
 */
declare(strict_types=1);

namespace Vass\Checkout\Plugin\Magento\Customer\CustomerData;

use Magento\Customer\CustomerData\Customer as ParentClass;
use Magento\Customer\Model\Session;

class Customer
{
    /**
     * Customer constructor.
     *
     * @param Session $customerSession
     */
    public function __construct(
        private readonly Session $customerSession
    ) {
    }

    /**
     * Add email to customer data
     *
     * @param ParentClass $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(
        ParentClass $subject,
        array $result
    ): array {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $result['email'] = $customer->getEmail();
            $result['document'] = $customer->getData('numero_de_identificacion') ?? '';
        }

        return $result;
    }
}
