<?php

namespace WolfSellers\Backend\Helper;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\WebsiteRepository;

class Data extends AbstractHelper
{

    /**
     * @var array
     */
    protected $configModule;
    protected $_storeManager;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private SourceRepositoryInterface $sourceRepository;
    private Session $authSession;
    private WebsiteRepository $websiteRepository;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @param Session $authSession
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        Session $authSession,
        WebsiteRepository $websiteRepository
    ){
        parent::__construct($context);
        $this->configModule = $this->getConfig(strtolower($this->_getModuleName()));
        $this->_storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->authSession = $authSession;
        $this->websiteRepository = $websiteRepository;
    }

    public function getConfig($cfg='')
    {
        if($cfg) return $this->scopeConfig->getValue( $cfg, ScopeInterface::SCOPE_STORE );
        return $this->scopeConfig;
    }

    public function getConfigModule($cfg='', $value=null)
    {
        $values = $this->configModule;
        if( !$cfg ) return $values;
        $config  = explode('/', $cfg);
        $end     = count($config) - 1;
        foreach ($config as $key => $vl) {
            if( isset($values[$vl]) ){
                if( $key == $end ) {
                    $value = $values[$vl];
                }else {
                    $values = $values[$vl];
                }
            }

        }
        return $value;
    }

    public function getBaseUrlMedia()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    public function getSourceName($source_code){
        if ($source_code == "all") {
            return "";
        }

        $codes = explode(",", $source_code);
        foreach ($codes as $code){
            $sourceData = $this->sourceRepository->get($code);
            if ($sourceData){
                $names[] = $sourceData->getName();
            }
        }

        return $names ? implode(",", $names) : '';
    }

    public function isBopis() {

        if($this->authSession->getUser() && $this->authSession->getUser()->getUserType() > 0) {
            return true;
        }
        return false;
    }

    public function getWebsiteManagerText() {
        if($this->authSession->getUser()->getWebsiteId() == 0) {
            return "Administrador de sucursales de todos los países";
        }
        $website = $this->websiteRepository->getById($this->authSession->getUser()->getWebsiteId());

        return "Administrador de sucursales de " . $website->getName();
    }

    /**
     * @return array
     */
    public function getSourcesList()
    {
        $search = $this->searchCriteriaBuilder->create();
        $sources = $this->sourceRepository->getList($search);
        $list = [];

        foreach ($sources->getItems() as $source){
            $list[$source->getSourceCode()] = $source->getName();
        }

        return $list;
    }
}
