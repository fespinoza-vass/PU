<?php
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();

if ($registrar->getPath(ComponentRegistrar::MODULE, 'adobe_adobeSignCheckout') === null) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'adobe_adobeSignCheckout', __DIR__);
}
