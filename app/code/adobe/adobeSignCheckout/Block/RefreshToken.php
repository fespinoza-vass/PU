<?php
namespace adobe\adobeSignCheckout\Block;

class RefreshToken extends \Magento\Backend\Block\Template
{
    protected $adobeSignConfigUrl;

    public function __construct(\Magento\Backend\Block\Template\Context $context)
    {
        parent::__construct($context);
        $this->adobeSignConfigUrl = $context->getUrlBuilder()->getUrl('adminhtml/system_config/edit/section/adobesign');
    }

    public function getAdobeSignConfigUrl()
    {
        return $this->adobeSignConfigUrl;
    }
}
