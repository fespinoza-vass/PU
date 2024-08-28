<?php

namespace WolfSellers\BackendBopis\Block\Adminhtml\Sales;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Gotoarchive implements ButtonProviderInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var Context
     */
    private $context;
    private \Magento\Backend\Model\UrlInterface $backendUrl;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \Magento\Backend\Model\Auth\Session $authSession;

    /**
     * CustomButton constructor.
     *
     * @param AuthorizationInterface $authorization
     * @param Context $context
     * @param UrlInterface $backendUrl
     * @param ResourceConnection $resourceConnection
     * @param Session $authSession
     */
    public function __construct(
        AuthorizationInterface $authorization,
        Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->authorization = $authorization;
        $this->context = $context;
        $this->backendUrl = $backendUrl;
        $this->resourceConnection = $resourceConnection;
        $this->authSession = $authSession;
    }

    public function getButtonData()
    {
        if ($this->authSession->getUser()->getData('user_type') != '1' && $this->authSession->getUser()->getData('user_type') != '2') {
            return [
                'label' => __('Go to Archive'),
                'on_click' => sprintf("location.href = '%s';", $this->backendUrl->getUrl("sales/archive/orders"))
            ];
        }
    }
}
