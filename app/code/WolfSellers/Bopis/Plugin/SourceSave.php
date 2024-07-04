<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use Magento\InventoryApi\Api\Data\SourceExtensionFactory;
use Magento\InventoryApi\Api\Data\SourceExtensionInterfaceFactory;
use Magento\InventoryApi\Api\Data\StockInterfaceFactory;


class SourceSave
{
    const CO_TIPO_DIRECCION = "co_tipo_direccion";
    const CO_STREET2 = "co_street2";
    const CO_STREET3 = "co_street3";
    const CO_ADDITIONAL_INFO = "co_additional_info";
    const CO_CIUDAD = "co_ciudad";


    const PE_DISTRITO = "pe_distrito";
    const PE_CIUDAD = "pe_ciudad";


    const CR_CANTON = "cr_canton";
    const CR_DISTRITO = "cr_distrito";

    protected SourceExtensionFactory $extensionFactory;

    /**
     * @param SourceExtensionFactory $extensionFactory
     */
    public function __construct(SourceExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param SourceRepositoryInterface $subject
     * @param SourceInterface $source
     * @return SourceInterface
     */
    public function afterGet(SourceRepositoryInterface $subject, SourceInterface $source)
    {
        $coTipoDireccion = $source->getData(self::CO_TIPO_DIRECCION);
        $coStreet2 = $source->getData(self::CO_STREET2);
        $coStreet3 = $source->getData(self::CO_STREET3);
        $coAdditionalInfo = $source->getData(self::CO_ADDITIONAL_INFO);
        $coCiudad = $source->getData(self::CO_CIUDAD);


        $peDistrito = $source->getData(self::PE_DISTRITO);
        $peCiudad = $source->getData(self::PE_CIUDAD);


        $crCanton = $source->getData(self::CR_CANTON);
        $crDistrito = $source->getData(self::CR_DISTRITO);
        $extensionAttributes = $source->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ?: $this->extensionFactory->create();
        $extensionAttributes->setCoTipoDireccion($coTipoDireccion);
        $extensionAttributes->setCoStreet2($coStreet2);
        $extensionAttributes->setCoStreet3($coStreet3);
        $extensionAttributes->setCoAdditionalInfo($coAdditionalInfo);
        $extensionAttributes->setCoCiudad($coCiudad);


        $extensionAttributes->setPeDistrito($peDistrito);
        $extensionAttributes->setPeCiudad($peCiudad);


        $extensionAttributes->setCrCanton($crCanton);
        $extensionAttributes->setCrDistrito($crDistrito);
        $source->setExtensionAttributes($extensionAttributes);

        return $source;
    }

    /**
     * @param SourceRepositoryInterface $subject
     * @param SourceSearchResultsInterface $result
     * @return SourceSearchResultsInterface
     */
    public function afterGetList(SourceRepositoryInterface $subject, SourceSearchResultsInterface $result)
    {
        $products = [];
        $sources = $result->getItems();

        foreach ($sources as $source) {
            $coTipoDireccion = $source->getData(self::CO_TIPO_DIRECCION);
            $coStreet2 = $source->getData(self::CO_STREET2);
            $coStreet3 = $source->getData(self::CO_STREET3);
            $coAdditionalInfo = $source->getData(self::CO_ADDITIONAL_INFO);
            $coCiudad = $source->getData(self::CO_CIUDAD);


            $peDistrito = $source->getData(self::PE_DISTRITO);
            $peCiudad = $source->getData(self::PE_CIUDAD);


            $crCanton = $source->getData(self::CR_CANTON);
            $crDistrito = $source->getData(self::CR_DISTRITO);
            // echo $sourceComment;
            $extensionAttributes = $source->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ?: $this->extensionFactory->create();
            $extensionAttributes->setCoTipoDireccion($coTipoDireccion);
            $extensionAttributes->setCoStreet2($coStreet2);
            $extensionAttributes->setCoStreet3($coStreet3);
            $extensionAttributes->setCoAdditionalInfo($coAdditionalInfo);
            $extensionAttributes->setCoCiudad($coCiudad);


            $extensionAttributes->setPeDistrito($peDistrito);
            $extensionAttributes->setPeCiudad($peCiudad);


            $extensionAttributes->setCrCanton($crCanton);
            $extensionAttributes->setCrDistrito($crDistrito);
            $source->setExtensionAttributes($extensionAttributes);
            $products[] = $source;
        }
        $result->setItems($products);
        return $result;
    }

    /**
     * @param SourceRepositoryInterface $subject
     * @param SourceInterface $source
     * @return SourceInterface[]
     */
    public function beforeSave(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ){
        $extensionAttributes = $source->getExtensionAttributes() ?: $this->extensionFactory->create();
        if ($extensionAttributes !== null && $extensionAttributes->getCoTipoDireccion() !== null) {
            $source->setCoTipoDireccion($extensionAttributes->getCoTipoDireccion());
        }
        if ($extensionAttributes !== null && $extensionAttributes->getCoStreet2() !== null) {
            $source->setData("co_street2", $extensionAttributes->getCoStreet2());
        }
        if ($extensionAttributes !== null && $extensionAttributes->getCoStreet3() !== null) {
            $source->setData("co_street3", $extensionAttributes->getCoStreet3());
        }
        if ($extensionAttributes !== null && $extensionAttributes->getCoAdditionalInfo() !== null) {
            $source->setCoAdditionalInfo($extensionAttributes->getCoAdditionalInfo());
        }
        if ($extensionAttributes !== null && $extensionAttributes->getCoCiudad() !== null) {
            $source->setCoCiudad($extensionAttributes->getCoCiudad());
        }


        if ($extensionAttributes !== null && $extensionAttributes->getPeDistrito() !== null) {
            $source->setPeDistrito($extensionAttributes->getPeDistrito());
        }
        if ($extensionAttributes !== null && $extensionAttributes->getPeCiudad() !== null) {
            $source->setPeCiudad($extensionAttributes->getPeCiudad());
        }


        if ($extensionAttributes !== null && $extensionAttributes->getCrCanton() !== null) {
            $source->setCrCanton($extensionAttributes->getCrCanton());
        }
        if ($extensionAttributes !== null && $extensionAttributes->getCrDistrito() !== null) {
            $source->setCrDistrito($extensionAttributes->getCrDistrito());
        }
        return [$source];
    }

}
