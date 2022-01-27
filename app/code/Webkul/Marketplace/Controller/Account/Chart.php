<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;

/**
 * Webkul Marketplace Chart Controller.
 */
class Chart extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     *      * @var \Webkul\Marketplace\Block\Account\Dashboard\Diagrams
     * @param Session     $customerSession
     */
    protected $diagrams;

    /**
     * @var \Webkul\Marketplace\Block\Account\Dashboard\LocationChart
     */
    protected $locationChart;

    /**
     * @var \Webkul\Marketplace\Block\Account\Dashboard\CategoryChart
     */
    protected $categoryChart;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * @param Context                                                     $context
     * @param Session                                                     $customerSession
     * @param \Webkul\Marketplace\Block\Account\Dashboard\Diagrams        $diagrams
     * @param \Webkul\Marketplace\Block\Account\Dashboard\LocationChart   $locationChart
     * @param \Webkul\Marketplace\Block\Account\Dashboard\CategoryChart   $categoryChart
     * @param \Magento\Framework\Json\Helper\Data                         $jsonHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Webkul\Marketplace\Block\Account\Dashboard\Diagrams $diagrams = null,
        \Webkul\Marketplace\Block\Account\Dashboard\LocationChart $locationChart = null,
        \Webkul\Marketplace\Block\Account\Dashboard\CategoryChart $categoryChart = null,
        \Magento\Framework\Json\Helper\Data $jsonHelper = null
    ) {
        $this->_customerSession = $customerSession;
        $this->diagrams = $diagrams ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Webkul\Marketplace\Block\Account\Dashboard\Diagrams::class);
        $this->locationChart = $locationChart ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Webkul\Marketplace\Block\Account\Dashboard\LocationChart::class);
        $this->categoryChart = $categoryChart ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Webkul\Marketplace\Block\Account\Dashboard\CategoryChart::class);
        $this->jsonHelper = $jsonHelper ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Framework\Json\Helper\Data::class);
        parent::__construct($context);
    }

    /**
     * Ask Query to seller action.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $chartUrl = '';
        if ($data['chartType'] == 'diagram') {
            $chartUrl = $this->diagrams->getSellerStatisticsGraphUrl($data['dateType']);
        } elseif ($data['chartType'] == 'location') {
            $chartUrl = $this->locationChart->getSellerStatisticsGraphUrl($data['dateType']);
        } elseif ($data['chartType'] == 'category') {
            $chartUrl = $this->categoryChart->getSellerStatisticsGraphUrl($data['dateType']);
        }
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($chartUrl)
        );
    }
}
