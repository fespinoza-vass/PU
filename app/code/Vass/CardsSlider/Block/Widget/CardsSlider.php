<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_CardsSlider
 * @author VASS Team
 */

namespace Vass\CardsSlider\Block\Widget;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class CardsSlider extends Template implements BlockInterface
{
    /**
     * Max value to deploy slides
     */
    private const MAX_SLIDES_AVAILABLE = 6;

    /**
     * Number of slidesPerView depends of screen format
     */
    private const SLIDES_IN_DESKTOP = 2.5;
    private const SLIDES_IN_TABLET = 2;
    private const SLIDES_IN_MOBILE = 1;

    /**
     * Template file for the widget
     *
     * @var string
     */
    protected $_template = 'widget/cards-slider.phtml';

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
     * Get the slider data
     *
     * @param string $type
     * @return array
     */
    private function getSliderData(string $type): array
    {
        $data = [];
        for ($i = 1; $i <= self::MAX_SLIDES_AVAILABLE; $i++) {
            $data[] = $this->getData("{$type}_{$i}");
        }
        return $data;
    }

    /**
     * Get the slider images
     *
     * @return array
     */
    public function getImages(): array
    {
        return $this->getSliderData('image');
    }

    /**
     * Get the slider title
     *
     * @return array
     */
    public function getTitle(): array
    {
        return $this->getSliderData('title');
    }

    /**
     * Get the slider description
     *
     * @return array
     */
    public function getDescription(): array
    {
        return $this->getSliderData('description');
    }

    /**
     * Get the slider Text URLs
     *
     * @return array
     */
    public function getTextUrl(): array
    {
        return $this->getSliderData('urlText');
    }

    /**
     * Get the slider URLs with optional routing and parameters
     *
     * @param string $route
     * @param array $params
     * @return array
     */
    public function getUrl($route = '', $params = [])
    {
        $urls = [];
        for ($i = 1; $i <= self::MAX_SLIDES_AVAILABLE; $i++) {
            $url = $this->getData("url_{$i}");
            if ($route) {
                $url = $this->getUrlBuilder()->getUrl($route, array_merge($params, ['id' => $i]));
            }
            $urls[] = $url;
        }
        return $urls;
    }

    /**
     * Get URL builder instance
     *
     * @return UrlInterface
     */
    protected function getUrlBuilder(): UrlInterface
    {
        return $this->_urlBuilder;
    }

    /**
     * Get the number of slides to show in desktop view
     *
     * @return float
     */
    public function getSlidesToShowInDesktop(): float
    {
        return self::SLIDES_IN_DESKTOP;
    }

    /**
     * Get the number of slides to show in tablet view
     *
     * @return int
     */
    public function getSlidesToShowInTablet(): int
    {
        return self::SLIDES_IN_TABLET;
    }

    /**
     * Get the number of slides to show in mobile view
     *
     * @return int
     */
    public function getSlidesToShowInMobile(): int
    {
        return self::SLIDES_IN_MOBILE;
    }
}
