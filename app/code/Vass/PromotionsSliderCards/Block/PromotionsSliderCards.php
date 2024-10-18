<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_PromotionsSliderCards
 * @author VASS Team
 */
declare(strict_types=1);

namespace Vass\PromotionsSliderCards\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class PromotionsSliderCards extends Template implements BlockInterface
{

    /**
     * Template file for the widget
     *
     * @var string
     */
    protected $_template = "widget/promotions.phtml";

    /**
     * Get the items of the slider
     *
     * @return array
     */
    public function getItems(): array
    {
        for ($i = 1; $i <= 3; $i++) {
            $data[] = [
                'main_image' => $this->getData('main_image_' . $i),
                'first_logo' => $this->getData('first_logo_' . $i),
                'secondary_logo' => $this->getData('secondary_logo_' . $i),
                'tertiary_logo' => $this->getData('tertiary_logo_' . $i),
                'title' => $this->getData('title_' . $i),
                'label_link' => $this->getData('label_link_' . $i),
                'link' => $this->getData('link_' . $i) ?? '#',
                'info' => $this->getData('info_' . $i),
            ];
        }

        return $data;
    }
}
