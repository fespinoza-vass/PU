<?php


namespace WolfSellers\Bopis\Block\Adminhtml;


use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 *
 */
class Bopis extends Template
{
    /**
     * @var Session
     */
    private Session $authSession;

    /**
     * @param Session $authSession
     * @param Context $context
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Session $authSession,
        Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ){
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->authSession = $authSession;
    }

    /**
     * @return bool
     */
    public function isBopis() {
        if($this->authSession->getUser()->getUserType() == 1) {
            return true;
        }
        return false;
    }
}
