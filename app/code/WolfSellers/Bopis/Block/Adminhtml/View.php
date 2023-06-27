<?php

namespace WolfSellers\Bopis\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Registry;
use Magento\Sales\Helper\Reorder;
use Magento\Sales\Model\ConfigInterface;

/**
 *
 */
class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    /**
     * @var Session
     */
    private Session $authSession;

    /**
     * @param Session $authSession
     * @param Context $context
     * @param Registry $registry
     * @param ConfigInterface $salesConfig
     * @param Reorder $reorderHelper
     * @param array $data
     */
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
