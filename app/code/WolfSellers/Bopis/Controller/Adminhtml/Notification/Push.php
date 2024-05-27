<?php
namespace WolfSellers\Bopis\Controller\Adminhtml\Notification;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Backend\App\Action;
use \Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Push extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Json $jsonSerializer
     * @param UrlInterface $urlBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param ScopeConfigInterface $_scopeConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Json $jsonSerializer,
        UrlInterface $urlBuilder,
        SortOrderBuilder $sortOrderBuilder,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        ScopeConfigInterface $_scopeConfig
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->urlBuilder = $urlBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->_scopeConfig = $_scopeConfig;
        parent::__construct($context);
    }

    /**
     * Get config
     *
     * @param String $path
     * @return String
     */
    protected function getConfig($path)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($path, $storeScope);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = ["result" => ""];
        $orderId = $this->getRequest()->getParam('orderId');
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter('state', 'processing', 'eq');
        $searchCriteriaBuilder->addFilter('status', $this->getConfig('bopis/status/confirmed'), 'eq');
        $searchCriteriaBuilder->addFilter('entity_id', $orderId, 'gt');
        $searchCriteriaBuilder->setPageSize(1);
        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection(SortOrder::SORT_ASC)
            ->create();
        $searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteria = $searchCriteriaBuilder->create();
        $list = $this->orderRepository->getList($searchCriteria);
        $items = $list->getItems();

        if ($list->getTotalCount() > 0) {
            $response["result"] = "success";
            foreach ($items as $item) {
                $response["orderId"] = $item->getEntityId();
                $response["order"] = $item->getIncrementId();
                $response["url"] =$this->urlBuilder->getUrl("*/order/view", ['order_id' => $item->getEntityId()]);
            }
            
        }

        return $this->jsonResponse($response);
    }

    /**
     * Create json response
     *
     * @param string $response
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonSerializer->serialize($response)
        );
    }
}
