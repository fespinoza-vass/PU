<?php

namespace WolfSellers\Checkout\Api\Data;

/**
 * Envio Rapido
 *
 * @codeCoverageIgnore
 * @api
 * @since 101.0.0
 */
interface RetiroTiendaInterface
{

    /**
     * Get CorreoOpcional
     *
     * @return string
     * @since 101.0.0
     */
    public function getCorreoOpcional();

    /**
     * Set CorreoOpcional
     *
     * @param string $correoOpcional
     * @return $this
     * @since 101.0.0
     */
    public function setCorreoOpcional($correoOpcional);


    /**
     * Get direccion_comprobante
     *
     * @return string
     * @since 101.0.0
     */
    public function getDireccionComprobante();

    /**
     * Set direccion_comprobante
     *
     * @param string $direccionComprobante
     * @return $this
     * @since 101.0.0
     */
    public function setDireccionComprobante($direccionComprobante);

    /**
     * Get Distrito Comprobante
     *
     * @return string
     * @since 101.0.0
     */
    public function getDistritoComprobante();

    /**
     * Set Distrito Comprobante
     *
     * @param string $distritoComprobante
     * @return $this
     * @since 101.0.0
     */
    public function setDistritoComprobante($distritoComprobante);

    /**
     * Get Identificacion
     *
     * @return string
     * @since 101.0.0
     */
    public function getIdentificacion();

    /**
     * Set
     *
     * @param string $identificacion
     * @return $this
     * @since 101.0.0
     */
    public function setIdentificacion($identificacion);

    /**
     * Get nombreApellido
     *
     * @return string
     * @since 101.0.0
     */
    public function getNombreApellido();

    /**
     * Set nombreApellido
     *
     * @param string $nombreApellido
     * @return $this
     * @since 101.0.0
     */
    public function setNombreApellido($nombreApellido);

    /**
     * Get Numero Identificacion
     *
     * @return string
     * @since 101.0.0
     */
    public function getNumeroIdentificacion();

    /**
     * Set Numero Identificacion
     *
     * @param string $numeroIdentificacion
     * @return $this
     * @since 101.0.0
     */
    public function setNumeroIdentificacion($numeroIdentificacion);

    /**
     * Get Picker
     *
     * @return string
     * @since 101.0.0
     */
    public function getPicker();

    /**
     * Set Picker
     *
     * @param string $picker
     * @return $this
     * @since 101.0.0
     */
    public function setPicker($picker);

}
