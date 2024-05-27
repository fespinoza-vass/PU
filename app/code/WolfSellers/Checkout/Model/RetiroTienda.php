<?php
declare(strict_types=1);

namespace WolfSellers\Checkout\Model;

use WolfSellers\Checkout\Api\Data\RetiroTiendaInterface;

/**
 *
 */
class RetiroTienda implements RetiroTiendaInterface
{
    /**
     * @var string
     */
    private string $picker;
    /**
     * @var string
     */
    private string $identificacion;
    /**
     * @var string
     */
    private string $numeroIdentificacion;
    /**
     * @var string
     */
    private string $nombreApellido;
    /**
     * @var string
     */
    private string $correoOpcional;
    /**
     * @var string
     */
    private string $distritoComprobante;
    /**
     * @var string
     */
    private string $direccionComprobante;

    /**
     * @return string
     */
    public function getPicker()
    {
        return $this->picker;
    }

    /**
     * @param string $picker
     */
    public function setPicker($picker)
    {
        $this->picker = $picker;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificacion()
    {
        return $this->identificacion;
    }

    /**
     * @param string $identificacion
     */
    public function setIdentificacion($identificacion)
    {
        $this->identificacion = $identificacion;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumeroIdentificacion()
    {
        return $this->numeroIdentificacion;
    }

    /**
     * @param string $numeroIdentificacion
     */
    public function setNumeroIdentificacion($numeroIdentificacion)
    {
        $this->numeroIdentificacion = $numeroIdentificacion;
        return $this;
    }

    /**
     * @return string
     */
    public function getNombreApellido()
    {
        return $this->nombreApellido;
    }

    /**
     * @param string $nombreApellido
     */
    public function setNombreApellido($nombreApellido)
    {
        $this->nombreApellido = $nombreApellido;
        return $this;
    }

    /**
     * @return string
     */
    public function getCorreoOpcional()
    {
        return $this->correoOpcional;
    }

    /**
     * @param string $correoOpcional
     */
    public function setCorreoOpcional($correoOpcional)
    {
        $this->correoOpcional = $correoOpcional;
        return $this;
    }

    /**
     * @return string
     */
    public function getDistritoComprobante()
    {
        return $this->distritoComprobante;
    }

    /**
     * @param string $distritoComprobante
     */
    public function setDistritoComprobante($distritoComprobante)
    {
        $this->distritoComprobante = $distritoComprobante;
        return $this;
    }

    /**
     * @return string
     */
    public function getDireccionComprobante()
    {
        return $this->direccionComprobante;
    }

    /**
     * @param string $direccionComprobante
     */
    public function setDireccionComprobante($direccionComprobante)
    {
        $this->direccionComprobante = $direccionComprobante;
        return $this;
    }



}
