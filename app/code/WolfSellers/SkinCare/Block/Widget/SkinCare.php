<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-04
 * Time: 10:51
 */

declare(strict_types=1);

namespace WolfSellers\SkinCare\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Skin Care Widget.
 */
class SkinCare extends Template implements BlockInterface
{
    protected $_template = 'widget/skin-care.phtml';

    /**
     * Get api key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return (string) $this->getDataByKey('api_key');
    }

    /**
     * Get width.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return (int) $this->getDataByKey('width');
    }

    /**
     * Get height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return (int) $this->getDataByKey('height');
    }

    /**
     * Get age spots max.
     *
     * @return int
     */
    public function getAgeSpotsMax(): int
    {
        return (int) $this->getDataByKey('age_spots_max');
    }

    /**
     * Get dark circles max.
     *
     * @return int
     */
    public function getDarkCirclesMax(): int
    {
        return (int) $this->getDataByKey('dark_circles_max');
    }

    /**
     * Get skin age max.
     *
     * @return int
     */
    public function getSkinAgeMax(): int
    {
        return (int) $this->getDataByKey('skin_age_max');
    }

    /**
     * Get skin health max.
     *
     * @return int
     */
    public function getSkinHealthMax(): int
    {
        return (int) $this->getDataByKey('skin_health_max');
    }

    /**
     * Get texture max.
     *
     * @return int
     */
    public function getTextureMax(): int
    {
        return (int) $this->getDataByKey('texture_max');
    }

    /**
     * Get wrinkles max.
     *
     * @return int
     */
    public function getWrinklesMax(): int
    {
        return (int) $this->getDataByKey('wrinkles_max');
    }
}
