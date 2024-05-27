<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-05-12
 * Time: 11:59
 */

declare(strict_types=1);

namespace WolfSellers\SkinCare\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Skin Care Bar Widget.
 */
class SkinCareBar extends Template implements BlockInterface
{
    protected $_template = 'widget/skin-care-bar.phtml';

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->getDataByKey('title');
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType(): string
    {
        return (string) $this->getDataByKey('result_type');
    }
}
