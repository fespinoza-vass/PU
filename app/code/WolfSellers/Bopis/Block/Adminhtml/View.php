<?php

namespace WolfSellers\Bopis\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Registry;
use Magento\Sales\Helper\Reorder;
use Magento\Sales\Model\ConfigInterface;

class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    private Session $authSession;

    public function __construct(
        Session $authSession,
        Context $context,
        Registry $registry,
        ConfigInterface $salesConfig,
        Reorder $reorderHelper,
        array $data = []
    ){
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
        $this->authSession = $authSession;
    }
    public function isBopis() {
        if($this->authSession->getUser()->getUserType() == 1) {
            return true;
        }
        return false;
    }
}
