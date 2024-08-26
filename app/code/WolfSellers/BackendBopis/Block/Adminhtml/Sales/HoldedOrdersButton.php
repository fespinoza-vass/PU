<?php

namespace WolfSellers\BackendBopis\Block\Adminhtml\Sales;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;

class HoldedOrdersButton implements ButtonProviderInterface
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
    private \WolfSellers\Bopis\Model\ResourceModel\HoldedOrder\Grid\CollectionFactory $collectionFactory;

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
        \WolfSellers\Bopis\Model\ResourceModel\HoldedOrder\Grid\CollectionFactory $collectionFactory,
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
                'label' => __($this->getHoldedOrders() . ' Detenidas'),
                'on_click' => sprintf("location.href = '%s';", $this->backendUrl->getUrl("bopis/listholdedorders/index")),
                'class' => 'bopis-atribute-color detenidas'
            ];
        }
    }

    public function getHoldedOrders(){
        $collection = $this->collectionFactory->create();
        return $collection->getSize();
    }
}
