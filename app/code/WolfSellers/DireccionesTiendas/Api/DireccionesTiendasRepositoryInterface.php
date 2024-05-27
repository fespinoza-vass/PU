<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface DireccionesTiendasRepositoryInterface
{

    /**
     * Save DireccionesTiendas
     * @param \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface $direccionesTiendas
     * @return \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface $direccionesTiendas
    );

    /**
     * Retrieve DireccionesTiendas
     * @param string $direccionestiendasId
     * @return \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($direccionestiendasId);

    /**
     * Retrieve DireccionesTiendas matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete DireccionesTiendas
     * @param \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface $direccionesTiendas
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface $direccionesTiendas
    );

    /**
     * Delete DireccionesTiendas by ID
     * @param string $direccionestiendasId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($direccionestiendasId);
}

