<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-05-12
 * Time: 12:03
 */

declare(strict_types=1);

namespace WolfSellers\SkinCare\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Skin Care Options result.
 */
class SkinCareOptions implements OptionSourceInterface
{
    public const AGE_SPOTS = 'ageSpots';
    public const DARK_CIRCLES = 'darkCircles';
    public const SKIN_AGE = 'skinAge';
    public const SKIN_HEALTH = 'skinHealth';
    public const TEXTURE = 'texture';
    public const WRINKLES = 'wrinkles';

    /**
     * Options.
     *
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::AGE_SPOTS, 'label' => __('Age spots')],
            ['value' => self::DARK_CIRCLES, 'label' => __('Dark circles')],
            ['value' => self::SKIN_AGE, 'label' => __('Skin age')],
            ['value' => self::SKIN_HEALTH, 'label' => __('Skin health')],
            ['value' => self::TEXTURE, 'label' => __('Texture')],
            ['value' => self::WRINKLES, 'label' => __('Wrinkles')],
        ];
    }
}
