<?php


namespace WolfSellers\Bopis\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Sales\Helper\Admin;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Helper\Data as TaxHelper;


class Title extends Footer
{
    /**
     * Config path to 'Translate Title' header settings
     */
    private const XML_PATH_HEADER_TRANSLATE_TITLE = 'design/header/translate_title';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Own page title to display on the page
     *
     * @var string
     */
    protected $pageTitle;
    private Registry $registry;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param array $data
     * @param ShippingHelper|null $shippingHelper
     * @param TaxHelper|null $taxHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        array $data = [],
        ?ShippingHelper $shippingHelper = null,
        ?TaxHelper $taxHelper = null
    ){
        parent::__construct($context, $registry, $adminHelper, $data, $shippingHelper, $taxHelper);
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
    }

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }

        $pageTitle = $this->pageConfig->getTitle()->getShort();

        return $this->shouldTranslateTitle() ? __($pageTitle) : $pageTitle;
    }

    /**
     * Provide own page content heading
     *
     * @return string
     */
    public function getPageHeading()
    {
        $pageTitle = !empty($this->pageTitle) ? $this->pageTitle : $this->pageConfig->getTitle()->getShortHeading();

        return $this->shouldTranslateTitle() ? __($pageTitle) : $pageTitle;
    }

    /**
     * Set own page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Check if page title should be translated
     *
     * @return bool
     */
    private function shouldTranslateTitle(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HEADER_TRANSLATE_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
