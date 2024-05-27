<?php

namespace WolfSellers\Checkout\Api\Data;

/**
 * Envio Rapido
 *
 * @codeCoverageIgnore
 * @api
 * @since 101.0.0
 */
interface EnvioRapidoInterface
{
    /**
     * Get Direccion
     *
     * @return string
     * @since 101.0.0
     */
    public function getDireccion();

    /**
     * Set $direccion
     *
     * @param string $direccion
     * @return $this
     * @since 101.0.0
     */
    public function setDireccion($direccion);

    /**
     * Get Distrito
     *
     * @return string
     * @since 101.0.0
     */
    public function getDistrito();

    /**
     * Set Distrito
     *
     * @param string $distrito
     * @return $this
     * @since 101.0.0
     */
    public function setDistrito($distrito);

    /**
     * Get HorarioSeleccionado
     *
     * @return string
     * @since 101.0.0
     */
    public function getHorarioSeleccionado();

    /**
     * Set HorarioSeleccionado
     *
     * @param string $horarioSeleccionado
     * @return $this
     * @since 101.0.0
     */
    public function setHorarioSeleccionado($horarioSeleccionado);

    /**
     * Get referencia
     *
     * @return string
     * @since 101.0.0
     */
    public function getReferencia();

    /**
     * Set Referencia
     *
     * @param string $referencia
     * @return $this
     * @since 101.0.0
     */
    public function setReferencia($referencia);

}
