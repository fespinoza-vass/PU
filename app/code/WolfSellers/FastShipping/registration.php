<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-05
 * Time: 10:25
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'WolfSellers_FastShipping',
    __DIR__
);
