<?php

namespace WolfSellers\HeaderLinks\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param Context $context
     * @param WysiwygConfig $_wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context                 $context,
        protected WysiwygConfig $_wysiwygConfig,
        array                   $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element): string
    {
        $element->setWysiwyg(true);
        $element->setConfig($this->_wysiwygConfig->getConfig($element));
        return parent::_getElementHtml($element);
    }
}
