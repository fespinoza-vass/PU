# WolfSellers_DireccionesTiendas
### Esta relacionado con el módulo WolfSellers_EnvioRapido
### Es la branch PULMDEV-87-MetodosEnvio. Esta branch nacio de integration y ya esta en integration
### La branch PULMDEV-87 contiene OTRO código NO MEZCLAR!
This module was created to improve the administration of Sources Address.
We use a new model "DireccionesTiendas", to add easily the required address attributes for the sources.

## Facts:

1. Peru needs the following data to create an Address
    1. Departamento
    2. Provincia
    3. Distrito
2. Merchant ask us to create "one-selector" for "Envío rápido". This selector ONLY uses DISTRICT. This DISTRICT MUST be
   unique. Merchant will do whatever they need to make DISTRICT
   unique. [If you google, some districts in Peru has the SAME name]
3. Based on the "direcciones_tiendas" model, we will get the additional information from the database. User only selects
   DISTRICT and developers need to get  (Departamento, Provincia) from the database and set information to Shipping
   Address

## Model "DireccionesTiendas"

1. The definition of each column of the model is
    1. ubigeo
    2. codigo_postal - Is the Zipcode of the physical store [Inventory Source]
    3. tienda - Is the Inventory Source [Stores > Inventory > Sources]. Can be unique, but... if you do unique and
       someone assign a source by mistake, they need to create new one to reassign. In production can have an impact.
    4. departamento - Is the State of the physical store [Inventory Source]
    5. provincia - Is the City of the physical store [Inventory Source]
    6. distrito - **UNIQUE**. Is like "County" or "Municipio" of the physical store [Inventory Source]
    7. direccion - Is the missing Address part of the physical store [Inventory Source], this can include street,
       department, external number, internal number
    8. referencia - There are some keywords or key places that help to find the address.

## Customer address attribute 'horarios_disponibles'

1. Horarios Disponibles with the attribute code horarios_disponibles MUST be created MANUALLY in de ADMIN, it will have
   the options:
    ['label' => __('Select hour...'), 'value' => 'default'],
    ['label' => __('Today from 12pm to 4pm'), 'value' => 'today__1200_1600'],
    ['label' => __('Today from 4pm to 8pm'), 'value' => 'today__1600_2000'],
    ['label' => __('Tomorrow from 12pm to 4pm'), 'value' => 'tomorrow__1200_1600'],
    ['label' => __('Tomorrow from 4pm to 8pm'), 'value' => 'tomorrow__1600_2000']
2. Rules

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

## Customer address attribute 'distrito_envio_rapido'

This attribute is NOT saved as an address, this attribute CANNOT be preserved in address forms

The relation between Data Patch, Plugin, db_schema, extension_attributes is:

1. Create the customer_address_attribute using **data patch**, with code 'distrito_envio_rapido'
2. Create the **payload-extender-mixin.js** and the **requirejs-config.js**.
3. In the extender, the value is read using jQuery selector.
    1. $('[name="custom_attributes[distrito_envio_rapido]"]').val();
4. Also in extender the value is sent using 'direccionestiendas_id'
    1. payload.addressInformation['extension_attributes']['direccionestiendas_id'] = parseInt(distrito_envio_rapido);
5. To use this extension_attribute name 'direccionestiendas_id', the attribute MUST be created in
   **extension_attributes.xml**
   ```xml
   <extension_attributes for="Magento\Checkout\Api\Data\ShippingInformationInterface">
      <attribute code="direccionestiendas_id" type="int"/>
   </extension_attributes>
   ```
6. To save this value in quote, you MUST create a column in quote table using **db_schema.xml**
    ```xml
    <table name="quote" resource="checkout" comment="Sales Flat Quote">
        <column xsi:type="int" name="direccionestiendas_id" unsigned="true" nullable="true"
                comment="Save direccionestiendas_id in quote"/>
    </table>
    ```

7. With the extension_attribute defined as 'direccionestiendas_id', and the quote table column name as '
   direccionestiendas_id', now you can use it in the **PHP plugin**, with **camelCase!**. This plugin intersects data of
   endpoint /V1/carts/mine/shipping-information OR /V1/guest-carts/:cartId/shipping-information
    ```php
    $extAttributes = $addressInformation->getExtensionAttributes();
    $idDireccionestiendas = $extAttributes->getDireccionestiendasId();
    $quote->setDireccionestiendasId($idDireccionestiendas);
    ```

8. Create columns for sales_order and sales_order_grid in db_schema.xml
    ```xml
    <table name="sales_order" resource="sales" comment="Sales Flat Order">
        <column xsi:type="text" name="direcciones_tiendas" nullable="true" comment="Save direccionestiendas as sent to Savar in order"/>
    </table>
    <table name="sales_order_grid" resource="sales" comment="Sales Flat Order Grid">
        <column xsi:type="text" name="direcciones_tiendas" nullable="true" comment="Save direccionestiendas as sent to Savar in order grid"/>
    </table>
    ```

9. Create observer to save quote field *direccionestiendas_id* in sales_order
    ```xml
    <event name="sales_model_service_quote_submit_before">
        <observer name="custom_fields_sales_address_save" instance="WolfSellers\DireccionesTiendas\Observer\SaveCustomFieldsInOrder" />
    </event>
    ```
    ```php
    $order = $observer->getEvent()->getOrder();
    $quote = $observer->getEvent()->getQuote();
    
    $value = "default";
    if (intval($quote->getDireccionestiendasId()) == 234) {
        $value = "It's the number 234";
    }
    $order->setData('direcciones_tiendas', $value);
    ```

10. Create a di.xml to save info from sales_order to sales_order_grid
    ```xml
    <virtualType name="Magento\Sales\Model\ResourceModel\Order\Grid" type="Magento\Sales\Model\ResourceModel\Grid">
        <arguments>
            <argument name="columns" xsi:type="array">
                <item name="direcciones_tiendas" xsi:type="string">sales_order.direcciones_tiendas</item>
            </argument>
        </arguments>
    </virtualType>
    ```


11. Create sales_order_grid.xml to show the new attribute in Admin > Sales Orders.
    1. WolfSellers/DireccionesTiendas/view/adminhtml/ui_component/sales_order_grid.xml

    ```xml
    <listing xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Ui:etc/ui_configuration.xsd">
        <columns name="sales_order_columns">
            <column name="direcciones_tiendas">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="filter" xsi:type="string">textRange</item>
                        <item name="bodyTmpl" xsi:type="string">ui/grid/cells/html</item>
                        <item name="label" xsi:type="string" translate="true">Dirección Tienda a Savar</item>
                    </item>
                </argument>
            </column>
        </columns>
    </listing>
    ```

Tested in:

- Magento 2 version 2.4.5-p1

Thanks to:

- Jeff Yu http://techjeffyu.com/blog/magento-2-add-a-custom-field-to-checkout-shipping
- MageAnts https://www.mageants.com/blog/how-to-create-order-attribute-programmatically-in-magento-2.html


