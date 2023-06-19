<?php


namespace WolfSellers\Bopis\Block\Adminhtml;


use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Bopis extends Template
{
    private Session $authSession;

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

    public function isBopis() {
        if($this->authSession->getUser()->getUserType() == 1) {
            return true;
        }
        return false;
    }
}
