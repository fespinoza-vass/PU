<?php

namespace Vass\CardsSlider\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class CardsSlider extends Template implements BlockInterface
{
    /**
     * Template file for the widget
     *
     * @var string
     */
    protected $_template = 'widget/cards_slider.phtml';

    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManager $storeManager
     */
    public function __construct(
        Context                $context,
        protected StoreManager $storeManager
    )
    {
        parent::__construct($context);
    }

    /**
     * Get the slider images
     *
     * @return array
     */
    public function getImages(): array
    {
        $image1 = $this->getData('image_1');
        $image2 = $this->getData('image_2');
        $image3 = $this->getData('image_3');
        $image4 = $this->getData('image_4');
        $image5 = $this->getData('image_5');
        $image6 = $this->getData('image_6');
        return [
            $image1,
            $image2,
            $image3,
            $image4,
            $image5,
            $image6];
    }

    /**
     * Get the slider title
     *
     * @return array
     */
    public function getTitle(): array
    {
        $title1 = $this->getData('title_1');
        $title2 = $this->getData('title_2');
        $title3 = $this->getData('title_3');
        $title4 = $this->getData('title_4');
        $title5 = $this->getData('title_5');
        $title6 = $this->getData('title_6');
        return [
            $title1,
            $title2,
            $title3,
            $title4,
            $title5,
            $title6
        ];
    }

    /**
     * Get the slider description
     *
     * @return array
     */
    public function getDescription(): array
    {
        $description1 = $this->getData('description_1');
        $description2 = $this->getData('description_2');
        $description3 = $this->getData('description_3');
        $description4 = $this->getData('description_4');
        $description5 = $this->getData('description_5');
        $description6 = $this->getData('description_6');
        return [
            $description1,
            $description2,
            $description3,
            $description4,
            $description5,
            $description6
        ];
    }

    /**
     * Get the slider Text URLs
     *
     * @return array
     */
    public function getTextUrl(): array
    {
        $urlText1 = $this->getData('urlText_1');
        $urlText2 = $this->getData('urlText_2');
        $urlText3 = $this->getData('urlText_3');
        $urlText4 = $this->getData('urlText_4');
        $urlText5 = $this->getData('urlText_5');
        $urlText6 = $this->getData('urlText_6');
        return [
            $urlText1,
            $urlText2,
            $urlText3,
            $urlText4,
            $urlText5,
            $urlText6
        ];
    }

    /**
     * Get the slider URLs
     *
     * @param string $route
     * @param array $params
     * @return array
     */
    public function getUrl($route = '', $params = []): array
    {
        $url1 = $this->getData('url_1');
        $url2 = $this->getData('url_2');
        $url3 = $this->getData('url_3');
        $url4 = $this->getData('url_4');
        $url5 = $this->getData('url_5');
        $url6 = $this->getData('url_6');
        return [
            $url1,
            $url2,
            $url3,
            $url4,
            $url5,
            $url6
        ];
    }
}
