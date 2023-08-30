<?php
namespace WolfSellers\Bopis\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Notifications extends Template
{
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
     *
     * @param ScopeConfigInterface $_scopeConfig
     * @param \Magento\Backend\Block\Template\Context $context
     * @param SortOrderBuilder $sortOrderBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        ScopeConfigInterface $_scopeConfig,
        \Magento\Backend\Block\Template\Context $context,
        SortOrderBuilder $sortOrderBuilder,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->_scopeConfig = $_scopeConfig;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
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
     * Return last order in processing
     *
     * @return int
     */
    public function getLastOrder()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        //$searchCriteriaBuilder->addFilter('state', 'processing', 'eq');
        //$searchCriteriaBuilder->addFilter('status', 'processing', 'eq');
        $searchCriteriaBuilder->setPageSize(1);
        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();
        $searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteria = $searchCriteriaBuilder->create();
        $list = $this->orderRepository->getList($searchCriteria);
        $items = $list->getItems();
        if ($list->getTotalCount() > 0) {
            foreach ($items as $item) {
                return $item->getEntityId();
            }
        }
        return 0;
    }

    /**
     * Get path sound
     *
     * @return String
     */
    public function getSound()
    {
        return $this->getConfig("bopis/pushnotification/sound");
    }

    /**
     * Get path icon
     *
     * @return String
     */
    public function getIcon()
    {
        return $this->getConfig("bopis/pushnotification/icon");
    }

    /**
     * Get seconds icon
     *
     * @return String
     */
    public function getSeconds()
    {
        return $this->getConfig("bopis/pushnotification/seconds");
    }

    /**
     * @return String
     */
    public function getNotificationText()
    {
        return $this->getConfig("bopis/pushnotification/notificationtext");
    }
}
