<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-04
 * Time: 10:49
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'WolfSellers_SkinCare',
    __DIR__
);
