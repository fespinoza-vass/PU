<?php

namespace WolfSellers\BackendBopis\Block\Adminhtml\Sales;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;
use WolfSellers\Bopis\Model\ResourceModel\CompleteOrder\Grid\Collection;
use WolfSellers\Bopis\Model\ResourceModel\CompleteOrder\Grid\CollectionFactory;

class CompleteOrdersButton implements ButtonProviderInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var Context
     */
    private $context;
    private UrlInterface $backendUrl;
    private ResourceConnection $resourceConnection;
    private Session $authSession;
    private CollectionFactory $collectionFactory;

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
        Context                $context,
        UrlInterface           $backendUrl,
        ResourceConnection     $resourceConnection,
        Session                $authSession,
        CollectionFactory      $collectionFactory = null
    ) {
        $this->authorization = $authorization;
        $this->context = $context;
        $this->backendUrl = $backendUrl;
        $this->resourceConnection = $resourceConnection;
        $this->authSession = $authSession;
        $this->collectionFactory = $collectionFactory ?? ObjectManager::getInstance()->create(CollectionFactory::class) ;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->authSession->getUser()->getData('user_type') == '1' || $this->authSession->getUser()->getData('user_type') == '2') {
            return [
                'label' => __($this->getCompleteOrders() . ' Entregadas'),
                'on_click' => sprintf("location.href = '%s';", $this->backendUrl->getUrl("bopis/listcompleteorders/index")),
                'class' => 'bopis-atribute-color entregadas'
            ];
        }
    }

    public function getCompleteOrders(){
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        return $collection->getSize();
    }
}
