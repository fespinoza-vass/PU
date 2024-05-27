<?php

namespace WolfSellers\BackendBopis\Block\Adminhtml\Sales;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ShippingOrdersButton implements ButtonProviderInterface
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
    private \WolfSellers\Bopis\Model\ResourceModel\ShippingOrder\Grid\CollectionFactory $collectionFactory;

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
        \WolfSellers\Bopis\Model\ResourceModel\ShippingOrder\Grid\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->authorization = $authorization;
        $this->context = $context;
        $this->backendUrl = $backendUrl;
        $this->collectionFactory = $collectionFactory;
        $this->authSession = $authSession;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->authSession->getUser()->getData('user_type') == '1' || $this->authSession->getUser()->getData('user_type') == '2') {
            return [
                'label' => __($this->getProcessingOrders() . ' En camino'),
                'on_click' => sprintf("location.href = '%s';", $this->backendUrl->getUrl("bopis/listshippingorders/index")),
                'class' => 'bopis-atribute-color en-camino'
            ];
        }
    }

    public function getProcessingOrders(){
        $collection = $this->collectionFactory->create();
        return $collection->getSize();
    }

}
