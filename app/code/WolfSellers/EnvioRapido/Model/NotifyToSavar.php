<?php

namespace WolfSellers\EnvioRapido\Model;

use Psr\Log\LoggerInterface;

use WolfSellers\EnvioRapido\Model\SavarApi;

class NotifyToSavar extends SavarApi
{

    CONST METHOD_TYPE = "post";

    protected function getRequest($data)
    {
        $requestPayload = [
            "CodPaquete" => "PRUEBA2023-013",
            "NomRemitente" => "Coolboxs",
            "DireccionRemitente" => "CALLE PRUEBA 123",
            "DistritoRemitente" => "LIMA|LIMA|CHORRILLOS",
            "TelefonoRemitente" => "959724456",
            "CodigoProducto" => "OPCIONAL_Max500_SKU5000",
            "MarcaProducto" => "OPCIONAL_Max500_MarcaEjemplo",
            "ModeloProducto" => "OPCIONAL_Max500_ModeloEjemplo",
            "ColorProducto" => "OPCIONAL_Max500_Azul",
            "TipoProducto" => "OPCIONAL_Max500_Electronico",
            "DescProducto" => "Descripcion del producto A, Descripcion del producto B",
            "cantidad" => 2,
            "NomConsignado" => "Max100_Cesar Ayulo Cribillero",
            "NumDocConsignado" => "Max20_4156565",
            "DireccionConsignado" => "Avenida Rio tiber",
            "DistritoConsignado" => "LIMA|LIMA|CHORRILLOS",
            "Referencia" => "OPCIONAL_Cerca hay un parque",
            "TelefonoConsignado" => "959878007",
            "CorreoConsignado" => "kevinayulo@gmail.com",
            "Subservicio" => "same day",
            "TipoPago" => "PREPAGADO",
            "MetodoPago" => "Efectivo",
            "Monto" => 10,
            "Largo" => 20.5,
            "Ancho" => 10.2,
            "Alto" => 5.2,
            "Peso" => 2.5,
            "ValorComercial" => 250,
            "HoraIni1" => "09:00",
            "HoraFin1" => "12:00",
            "HoraIni2" => "14:00",
            "HoraFin2" => "18:00",
            "Comentario" => "Entregar a la puerta principal",
            "Comentario2" => "prueba comentario dos",
            "Latitud" => "-12.0938175",
            "Longitud" => "-77.0426187"
        ];

        return $requestPayload;
    }

    protected function getUri()
    {
        return $this->getBaseUrl();
    }

    protected function getMethodType()
    {
        return self::METHOD_TYPE;
    }
}
