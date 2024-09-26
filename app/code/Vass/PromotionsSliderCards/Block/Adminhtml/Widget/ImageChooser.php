<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_PromotionsSliderCards
 * @author VASS Team
 */
declare(strict_types=1);

namespace Vass\PromotionsSliderCards\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Exception\LocalizedException;

class ImageChooser extends Template
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly Factory $elementFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl(
            'cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']
        );
        $chooser = $this->getLayout()->createBlock(Button::class)
            ->setType('button')
            ->setClass('btn-chooser')
            ->setLabel($config['button']['open'])
            ->setOnClick('MediabrowserUtility.openDialog(\'' . $sourceUrl . '\')')
            ->setDisabled($element->getReadonly());

        $input = $this->elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("widget-option input-text admin__control-text");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $element->setData('after_element_html', $input->getElementHtml() . $chooser->toHtml());
        return $element;
    }
}
