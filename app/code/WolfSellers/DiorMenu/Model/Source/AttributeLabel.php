<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-05-02
 * Time: 23:19
 */

declare(strict_types=1);

namespace WolfSellers\DiorMenu\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Attribute Label Options.
 */
class AttributeLabel implements OptionSourceInterface
{
    public const CATEGORY_ATTR = 'categoria';
    public const LINE_ATTR = 'linea';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::CATEGORY_ATTR, 'label' => __('Category')],
            ['value' => self::LINE_ATTR, 'label' => __('Line')],
        ];
    }
}
