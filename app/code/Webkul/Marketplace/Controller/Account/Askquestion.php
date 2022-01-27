<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Webkul Marketplace Askquestion Controller.
 */
class Askquestion extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param MarketplaceHelper $marketplaceHelper
     * @param MpEmailHelper $mpEmailHelper
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        MarketplaceHelper $marketplaceHelper = null,
        MpEmailHelper $mpEmailHelper = null,
        JsonHelper $jsonHelper = null
    ) {
        $this->_customerSession = $customerSession;
        $this->marketplaceHelper = $marketplaceHelper ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(MarketplaceHelper::class);
        $this->mpEmailHelper = $mpEmailHelper ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(MpEmailHelper::class);
        $this->jsonHelper = $jsonHelper ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(JsonHelper::class);
        parent::__construct($context);
    }

    /**
     * Ask Query to seller action.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $helper = $this->marketplaceHelper;

        $sellerName = $this->_customerSession->getCustomer()->getName();
        $sellerEmail = $this->_customerSession->getCustomer()->getEmail();

        $adminStoremail = $helper->getAdminEmailId();
        $adminEmail = $adminStoremail ? $adminStoremail : $helper->getDefaultTransEmailId();
        $adminUsername = $helper->getAdminName();

        $emailTemplateVariables = [];
        $senderInfo = [];
        $receiverInfo = [];
        $emailTemplateVariables['myvar1'] = $adminUsername;
        $emailTemplateVariables['myvar2'] = $sellerName;
        $emailTemplateVariables['subject'] = $data['subject'];
        $emailTemplateVariables['myvar3'] = $data['ask'];
        $senderInfo = [
            'name' => $sellerName,
            'email' => $sellerEmail,
        ];
        $receiverInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];
        $this->mpEmailHelper->askQueryAdminEmail($emailTemplateVariables, $senderInfo, $receiverInfo);
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode('true')
        );
    }
}
