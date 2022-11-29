<?php
namespace WolfSellers\Customer\Block;

class TypePage extends \Magento\Framework\View\Element\Template
{
    protected $context;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Page\Title $pageTitle,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->context = $context;
        $this->_pageTitle = $pageTitle;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    
    public function getTypePage() {
        $pageType = '';
        $category = $this->_registry->registry('current_category');
        if($category){
            $pageCategory = $category->getName();
        }else{
            $pageCategory = '';
        }
        $currentPage = $this->_request->getFullActionName();
        
        if ($currentPage == 'catalog_category_view') {
            $pageType = 'Category';
        }
        if ($currentPage == 'cms_index_index') {
            $pageType = 'Home';
        }
        if ($currentPage == 'cms_page_view') {
            $pageType = 'Page';
        }
        if ($currentPage == 'catalog_product_view') {
            $pageType = 'Product';
        }
        if ($currentPage == 'catalogsearch_result_index') {
            $pageType = 'Search Result';
        }
        if ($currentPage == 'customer_account_login') {
            $pageType = 'Login';
        }
        
        $data = [
            'pageType' => $pageType,
            'pageCategory' => $pageCategory,
            'pageTitle' => $this->_pageTitle->getShort()
        ];
        
        return $data;
    }
}