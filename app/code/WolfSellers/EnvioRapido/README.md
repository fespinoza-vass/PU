# WolfSellers_EnvioRapido
### Esta relacionado con el módulo WolfSellers_DireccionesTiendas
### Es la branch PULMDEV-87-MetodosEnvio. Esta branch nacio de integration y ya esta en integration
### La branch PULMDEV-87 contiene OTRO código NO MEZCLAR!
### La branch PULMDEV-87 puede ser de ayuda

Este módulo contiene lo necesario para crear un método de envío llamado "Envío Rápido" el carrier incluido para este método es "Savar"



1. Create shipping method
   1. WolfSellers/EnvioRapido/etc/adminhtml/system.xml
   2. WolfSellers/EnvioRapido/etc/config.xml
   3. WolfSellers/EnvioRapido/Model/Carrier/EnvioRapido.php
   4. Add Magento_Shipping & Magento_Quote as dependency in module.xml
2. Create carrier configuration options and  Model/Configuration file to get information
   1. WolfSellers/EnvioRapido/etc/adminhtml/system.xml
   2. WolfSellers/EnvioRapido/etc/config.xml
   3. WolfSellers/EnvioRapido/Model/Configuration.php
