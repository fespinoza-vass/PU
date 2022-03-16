<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-03-10
 * Time: 14:08
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Packaging.
 */
class Packaging implements OptionSourceInterface
{
    public const PACKAGE = 'PQ';
    public const ENVELOPE = 'SO';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::PACKAGE, 'label' => __('Paquetes')],
            ['value' => self::ENVELOPE, 'label' => __('Sobres')],
        ];
    }
}
