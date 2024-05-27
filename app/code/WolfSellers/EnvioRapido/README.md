# WolfSellers_EnvioRapido

### Esta relacionado con el módulo WolfSellers_DireccionesTiendas

### Es la branch PULMDEV-87-MetodosEnvio. Esta branch nacio de integration y ya esta en integration

### La branch PULMDEV-87 contiene OTRO código NO MEZCLAR!


Este módulo contiene lo necesario para crear un método de envío llamado "Envío Rápido", el carrier incluido para este
método es "SavarExpress"

1. Create shipping method
    1. WolfSellers/EnvioRapido/etc/adminhtml/system.xml
    2. WolfSellers/EnvioRapido/etc/config.xml
    3. WolfSellers/EnvioRapido/Model/Carrier/EnvioRapido.php
    4. Add Magento_Shipping & Magento_Quote as dependency in module.xml
2. Create carrier configuration options and Model/Configuration file to get information
    1. WolfSellers/EnvioRapido/etc/adminhtml/system.xml
    2. WolfSellers/EnvioRapido/etc/config.xml
    3. WolfSellers/EnvioRapido/Model/Configuration.php
3. Falta crear el API para notificar a Savar Express del envío. NOTA: Recordemos que los desarrolladores quienes esta
   preparando los dashboards de gestión serán quienes disparen el evento de NOTIFICAR A SAVAR. Incluso podrían utlizar la
   funcion _doShipmentRequest en el archivo de Model/Carrier/EnvioRapido.php De nuestro lado deberemos dejar listo la
   lógica en el archivo:
    1. WolfSellers/EnvioRapido/Model/NotifyToSavar.php
4. Falta crear el webhook, en el cual Savar nos informará que el pedido fue entregado. Falta una sesión para saber ¿Cómo
   se harán pruebas?. Incluso, podría terminar siendo un CRON que este consultando cada cierto tiempo el estatus del
   pedido.
5. Falta que los PMs nos apoyen con las preguntas escritas en la sección de comentarios del ticket:
    1. https://wolfsellers.atlassian.net/browse/PULMDEV-87
