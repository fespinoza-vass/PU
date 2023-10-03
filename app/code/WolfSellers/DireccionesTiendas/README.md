# WolfSellers_DireccionesTiendas

This module was created to improve the administration of Sources Address.
We use a new model "DireccionesTiendas", to add easily the required address attributes for the sources.

Facts:

1. Peru needs the following data to create an Address
    1. Departamento
    2. Provincia
    3. Distrito
2. Merchant ask us to create "one-selector" for "Envío rápido". This selector ONLY uses DISTRICT. This DISTRICT MUST be
   unique. Merchant will do whatever they need to make DISTRICT
   unique. [If you google, some districts in Peru has the SAME name]
3. Based on the "direcciones_tiendas" model, we will get the additional information from the database. User only selects
   DISTRICT and developers need to get  (Departamento, Provincia) from the database.

