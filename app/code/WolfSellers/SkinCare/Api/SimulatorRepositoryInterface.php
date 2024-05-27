<?php

namespace WolfSellers\SkinCare\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use WolfSellers\SkinCare\Api\Data\SimulatorInterface;
use WolfSellers\SkinCare\Api\Data\SimulatorSearchResultsInterface;

interface SimulatorRepositoryInterface
{

    /**
     * @param SimulatorInterface $simulator
     * @throws LocalizedException
     * @return mixed
     */
    public function save(SimulatorInterface $simulator);

    /**
     * @param $id
     * @return mixed
     * @throws LocalizedException
     */
    public function get($id);

    /**
     * @param $formId
     * @return mixed
     * @throws LocalizedException
     */
    public function getByFormId($formId);

    /**
     * @param $email
     * @return mixed
     * @throws LocalizedException
     */
    public function getByEmail($email);

    /**
     * @param $email
     * @return mixed
     * @throws LocalizedException
     */
    public function getByFormType($formId, $type);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SimulatorSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param SimulatorInterface $simulator
     * @return mixed
     * @throws LocalizedException
     */
    public function delete(SimulatorInterface $simulator);

    /**
     * @param $id
     * @return mixed
     * @throws LocalizedException
     */
    public function deleteById($id);

}
