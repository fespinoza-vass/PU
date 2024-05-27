<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Model;

use Magento\Framework\Model\AbstractModel;
use WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface;

class DireccionesTiendas extends AbstractModel implements DireccionesTiendasInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\WolfSellers\DireccionesTiendas\Model\ResourceModel\DireccionesTiendas::class);
    }

    /**
     * @inheritDoc
     */
    public function getDireccionestiendasId()
    {
        return $this->getData(self::DIRECCIONESTIENDAS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setDireccionestiendasId($direccionestiendasId)
    {
        return $this->setData(self::DIRECCIONESTIENDAS_ID, $direccionestiendasId);
    }

    /**
     * @inheritDoc
     */
    public function getUbigeo()
    {
        return $this->getData(self::UBIGEO);
    }

    /**
     * @inheritDoc
     */
    public function setUbigeo($ubigeo)
    {
        return $this->setData(self::UBIGEO, $ubigeo);
    }

    /**
     * @inheritDoc
     */
    public function getCodigoPostal()
    {
        return $this->getData(self::CODIGO_POSTAL);
    }

    /**
     * @inheritDoc
     */
    public function setCodigoPostal($codigoPostal)
    {
        return $this->setData(self::CODIGO_POSTAL, $codigoPostal);
    }

    /**
     * @inheritDoc
     */
    public function getTienda()
    {
        return $this->getData(self::TIENDA);
    }

    /**
     * @inheritDoc
     */
    public function setTienda($tienda)
    {
        return $this->setData(self::TIENDA, $tienda);
    }

    /**
     * @inheritDoc
     */
    public function getDepartamento()
    {
        return $this->getData(self::DEPARTAMENTO);
    }

    /**
     * @inheritDoc
     */
    public function setDepartamento($departamento)
    {
        return $this->setData(self::DEPARTAMENTO, $departamento);
    }

    /**
     * @inheritDoc
     */
    public function getProvincia()
    {
        return $this->getData(self::PROVINCIA);
    }

    /**
     * @inheritDoc
     */
    public function setProvincia($provincia)
    {
        return $this->setData(self::PROVINCIA, $provincia);
    }

    /**
     * @inheritDoc
     */
    public function getDistrito()
    {
        return $this->getData(self::DISTRITO);
    }

    /**
     * @inheritDoc
     */
    public function setDistrito($distrito)
    {
        return $this->setData(self::DISTRITO, $distrito);
    }

    /**
     * @inheritDoc
     */
    public function getDireccion()
    {
        return $this->getData(self::DIRECCION);
    }

    /**
     * @inheritDoc
     */
    public function setDireccion($direccion)
    {
        return $this->setData(self::DIRECCION, $direccion);
    }

    /**
     * @inheritDoc
     */
    public function getReferencia()
    {
        return $this->getData(self::REFERENCIA);
    }

    /**
     * @inheritDoc
     */
    public function setReferencia($referencia)
    {
        return $this->setData(self::REFERENCIA, $referencia);
    }
}

