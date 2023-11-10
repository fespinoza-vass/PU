<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Api\Data;

interface DireccionesTiendasInterface
{

    const CODIGO_POSTAL = 'codigo_postal';
    const DEPARTAMENTO = 'departamento';
    const DISTRITO = 'distrito';
    const DIRECCION = 'direccion';
    const PROVINCIA = 'provincia';
    const UBIGEO = 'ubigeo';
    const DIRECCIONESTIENDAS_ID = 'direccionestiendas_id';
    const TIENDA = 'tienda';
    const REFERENCIA = 'referencia';

    /**
     * Get direccionestiendas_id
     * @return string|null
     */
    public function getDireccionestiendasId();

    /**
     * Set direccionestiendas_id
     * @param string $direccionestiendasId
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setDireccionestiendasId($direccionestiendasId);

    /**
     * Get ubigeo
     * @return string|null
     */
    public function getUbigeo();

    /**
     * Set ubigeo
     * @param string $ubigeo
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setUbigeo($ubigeo);

    /**
     * Get codigo_postal
     * @return string|null
     */
    public function getCodigoPostal();

    /**
     * Set codigo_postal
     * @param string $codigoPostal
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setCodigoPostal($codigoPostal);

    /**
     * Get tienda
     * @return string|null
     */
    public function getTienda();

    /**
     * Set tienda
     * @param string $tienda
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setTienda($tienda);

    /**
     * Get departamento
     * @return string|null
     */
    public function getDepartamento();

    /**
     * Set departamento
     * @param string $departamento
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setDepartamento($departamento);

    /**
     * Get provincia
     * @return string|null
     */
    public function getProvincia();

    /**
     * Set provincia
     * @param string $provincia
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setProvincia($provincia);

    /**
     * Get distrito
     * @return string|null
     */
    public function getDistrito();

    /**
     * Set distrito
     * @param string $distrito
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setDistrito($distrito);

    /**
     * Get direccion
     * @return string|null
     */
    public function getDireccion();

    /**
     * Set direccion
     * @param string $direccion
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setDireccion($direccion);

    /**
     * Get referencia
     * @return string|null
     */
    public function getReferencia();

    /**
     * Set referencia
     * @param string $referencia
     * @return \WolfSellers\DireccionesTiendas\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     */
    public function setReferencia($referencia);
}

