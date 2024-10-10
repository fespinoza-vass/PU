# Izipay Core

Extensión para implementar el boton de pago de Izipay en Magento2 (v2.4.x)

## Instalación del módulo

Ir a la carpeta raíz del proyecto de Magento y seguir los siguiente pasos:

```bash    
composer require izipay/mage2-core:1.0.*
php bin/magento module:enable Izipay_Core --clear-static-content
php bin/magento setup:upgrade
php bin/magento cache:clean
```


