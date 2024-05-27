<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-05-05
 * Time: 15:48
 */

declare(strict_types=1);

namespace WolfSellers\Customer\Block;

use Magento\Customer\Block\Account\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Customer Login Account.
 */
class LoginAccount extends Customer
{
    /** @var CustomerSession */
    private CustomerSession $customerSession;

    /**
     * Constructor.
     *
     * @param TemplateContext $context
     * @param HttpContext $httpContext
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        HttpContext $httpContext,
        CustomerSession $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $data);
        $this->customerSession = $customerSession;
    }

    /**
     * Get customer fullname.
     *
     * @return string
     */
    public function getFullname(): string
    {
        return $this->customerSession->getCustomer()->getName();
    }
}
