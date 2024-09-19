<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_PromotionsSlider
 * @author VASS Team
 */
declare(strict_types=1);

namespace Vass\PromotionsSlider\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class PromotionsSlider extends Template implements BlockInterface
{

    /**
     * Template file for the widget
     *
     * @var string
     */
    protected $_template = "widget/promotions.phtml";

    /**
     * Get the title of the slider
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData('title');
    }

    /**
     * Get the subtitle of the slider
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->getData('subtitle');
    }

    /**
     * Get the items of the slider
     *
     * @return array
     */
    public function getItems(): array
    {
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'image' => $this->getData('image_' . $i) ?? '',
                'label' => $this->getData('label_' . $i) ?? '',
                'link' => $this->getData('link_' . $i) ?? '',
            ];
        }

        return $data;
    }
}
