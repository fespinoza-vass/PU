<?php
declare(strict_types=1);
namespace WolfSellers\Checkout\Api\Data;

/**
 * Envio Rapido
 *
 * @codeCoverageIgnore
 * @api
 * @since 101.0.0
 */
interface EnvioRegularInterface
{

    /**
     * Get Departamento
     *
     * @return string
     * @since 101.0.0
     */
    public function getDepartamento();

    /**
     * Set Departamento
     *
     * @param string $departamento
     * @return $this
     * @since 101.0.0
     */
    public function setDepartamento($departamento);


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
     * Get Metodo de envio
     *
     * @return string
     * @since 101.0.0
     */
    public function getMetodoEnvio();

    /**
     * Set Metodo de envio
     *
     * @param string $metodoEnvio
     * @return $this
     * @since 101.0.0
     */
    public function setMetodoEnvio($metodoEnvio);

    /**
     * Get provincia
     *
     * @return string
     * @since 101.0.0
     */
    public function getProvincia();

    /**
     * Set Provincia
     *
     * @param string $provincia
     * @return $this
     * @since 101.0.0
     */
    public function setProvincia($provincia);

    /**
     * Get Referencia
     *
     * @return string
     * @since 101.0.0
     */
    public function getReferencia();

    /**
     * Set Provincia
     *
     * @param string $referencia
     * @return $this
     * @since 101.0.0
     */
    public function setReferencia($referencia);

}
