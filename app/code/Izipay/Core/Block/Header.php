<?php
namespace Izipay\Core\Block;

use \Izipay\Core\Helper\Data as IzipayHelper;
use \Magento\Framework\View\Element\Template\Context;
use \Izipay\Core\Model\IzipayFactory;
use \Magento\Framework\View\Element\Template;
use \Izipay\Core\Logger\Logger;

class Header extends Template
{
    protected $_helper;
    protected $_logger;
    
    public function __construct(
        IzipayHelper $_helper,
        Logger $_logger,
        Context $context
    ) {
        $this->_helper = $_helper;
        $this->_logger = $_logger;

        parent::__construct($context);
    }

    public function getUrlSdk()
    {   
        return $this->_helper->getUrlSdk();
    }

   
}
                                                                                                                                                                        