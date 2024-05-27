<?php
declare(strict_types=1);

namespace WolfSellers\Checkout\Model;

use WolfSellers\Checkout\Api\Data\EnvioRegularInterface;

class EnvioRegular implements EnvioRegularInterface
{

    /**
     * @var string
     */
    private string $departamento;
    /**
     * @var string
     */
    private string $direccion;
    /**
     * @var string
     */
    private string $distrito;
    /**
     * @var string
     */
    private string $metodoEnvio;
    /**
     * @var string
     */
    private string $provincia;

    /**
     * @var string
     */
    private string $referencia;

    /**
     * @return string
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * @param string $departamento
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
        return $this;
    }

    /**
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
        return $this;
    }

    /**
     * @return string
     */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /**
     * @param string $distrito
     */
    public function setDistrito($distrito)
    {
        $this->distrito = $distrito;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetodoEnvio()
    {
        return $this->metodoEnvio;
    }

    /**
     * @param string $metodoEnvio
     */
    public function setMetodoEnvio($metodoEnvio)
    {
        $this->metodoEnvio = $metodoEnvio;
        return $this;
    }

    /**
     * @return string
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param string $provincia
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * @param string $referencia
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
        return $this;
    }
}
