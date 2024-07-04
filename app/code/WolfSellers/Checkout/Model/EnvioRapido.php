<?php
declare(strict_types=1);

namespace WolfSellers\Checkout\Model;

use WolfSellers\Checkout\Api\Data\EnvioRapidoInterface;

class EnvioRapido implements EnvioRapidoInterface
{

    /**
     * @var string
     */
    private string $horarioSeleccionado;
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
    private string $referencia;

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

    /**
     * @return string
     */
    public function getHorarioSeleccionado()
    {
        return $this->horarioSeleccionado;
    }

    /**
     * @param string $horarioSeleccionado
     */
    public function setHorarioSeleccionado($horarioSeleccionado)
    {
        $this->horarioSeleccionado = $horarioSeleccionado;
        return $this;
    }


}
