# WolfSellers_EnvioRapido

Este módulo contiene lo necesario para crear un método de envío llamado "Envío Rápido" el carrier incluido para este método es "Savar"

1. Create shipping method
   1. WolfSellers/EnvioRapido/etc/adminhtml/system.xml
   2. WolfSellers/EnvioRapido/etc/config.xml
   3. WolfSellers/EnvioRapido/Model/Carrier/EnvioRapido.php
   4. Add Magento_Shipping & Magento_Quote as dependency in module.xml
2. Add Customer Address Attributes to save new requested data
   1. Distrito distrito_envio_rapido -> Display available districts in "savar express zone"
   2. Horarios Disponibles horarios_disponibles -> 
      1. HOY de 12:00 a 16:00 today__1200_1600
      2. HOY de 16:00 a 20:00 today__1600_2000
      3. MAÑANA de 12:00 a 16:00 tomorrow__1200_1600
      4. MAÑANA de 16:00 a 20:00 tomorrow__1600_2000
      5. Reglas
```php
// SIEMPRE se muestran las opciones: Entrega de 12:00 a 16:00 y de 16:00 a 20:00
// Pero dependiendo la hora actual en el navegador, la hora del envío y el texto serán DIFERENTES 
// Considerando 1 hora para la preparación del paquete y 1 hora de envío.
// Adicional, se programará con una ventana de 4 horas en caso de devolución.
// Sus horarios son de 10:00 a 22:00
if(hora_actual >= 0:00 && hora_actual < 14:00) { 
    //OPCIÓN 1: Usuario selecciona de 12:00 a 16:00 
    $textToShowInFront = 'Tu pedido llegará HOY <diaDeLaSemana> <diaDelMes> <mes> en un rango de 12 a 4pm';
    // La opcion que se deberá elegir en el Address Attribute es today__1200_1600
    $horarios_disponibles = 'today__1200_1600';
    
    //OPCIÓN 2: Usuario selecciona de 16:00 a 20:00
    $textToShowInFront = 'Tu pedido llegará HOY <diaDeLaSemana> <diaDelMes> <mes> en un rango de 4 a 8pm';
    // La opcion que se deberá elegir en el Address Attribute es today__1600_2000
    $horarios_disponibles = 'today__1600_2000';
}
elseif(hora_actual >= 14:00 && hora_actual < 18:00){
    //OPCIÓN 1: Usuario selecciona de 12:00 a 16:00 
    $textToShowInFront = 'Tu pedido llegará MAÑANA <diaDeLaSemana> <diaDelMes> <mes> en un rango de 12 a 4pm';
    // La opcion que se deberá elegir en el Address Attribute es tomorrow__1200_1600
    $horarios_disponibles = 'tomorrow__1200_1600';
    
    //OPCIÓN 2: Usuario selecciona de 16:00 a 20:00 
     $textToShowInFront = 'Tu pedido llegará HOY <diaDeLaSemana> <diaDelMes> <mes> en un rango de 4 a 8pm';
    // La opcion que se deberá elegir en el Address Attribute es today__1600_2000
    $horarios_disponibles = 'today__1600_2000';

}
else{
    //OPCIÓN 1: Usuario selecciona de 12:00 a 16:00 
    $textToShowInFront = 'Tu pedido llegará MAÑANA <diaDeLaSemana> <diaDelMes> <mes> en un rango de 12 a 4pm';
    // La opcion que se deberá elegir en el Address Attribute es tomorrow__1200_1600
    $horarios_disponibles = 'tomorrow__1200_1600';
    
    //OPCIÓN 2: Usuario selecciona de 16:00 a 20:00 
     $textToShowInFront = 'Tu pedido llegará MAÑANA <diaDeLaSemana> <diaDelMes> <mes> en un rango de 4 a 8pm';
    // La opcion que se deberá elegir en el Address Attribute es tomorrow__1600_2000
    $horarios_disponibles = 'tomorrow__1600_2000';
}
```

