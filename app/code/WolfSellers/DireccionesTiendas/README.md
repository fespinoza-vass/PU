# WolfSellers_DireccionesTiendas

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
    6. distrito - UNIQUE. Is like "County" or "Municipio" of the physical store [Inventory Source]
    7. direccion - Is the missing Address part of the physical store [Inventory Source], this can include street,
       department, external number, internal number
    8. referencia - There are some keywords or key places that help to find the address.

## Customer address attribute 'distrito_envio_rapido'

1. A customer_address_attribute was "created", between quotes because it was created, but It doesn't save the data. We
   need this attribute in this way because the origin of the data comes from model "DireccionesTiendas", then we CAN'T
   create it manual, because the information NEED to be related to one Inventory Source. This model will be updated by
   non-developers staff
2. This data will be "intersected" or getted using a plugin to endpoint /rest/default/V1/carts/mine/shipping-information
   y /V1/guest-carts/:cartId/shipping-information
   /rest/default/V1/guest-carts/LeewVLSkPkVwQKchUHOjFvKhtawNdX8w/shipping-information
3. This data will be saved in the quote. A new field was created to save this data, as seen in the file:
4. Then, the data will be saved in the sales_order and sales_order_grid, using the observer:
5. To save this data in order, we add a new field in sales_order and sales_order_grid as seen in the file:

