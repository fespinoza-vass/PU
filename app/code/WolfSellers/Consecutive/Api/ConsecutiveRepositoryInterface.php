<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ConsecutiveRepositoryInterface
{

    /**
     * Save consecutive
     * @param \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface $consecutive
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface $consecutive
    );

    /**
     * Retrieve consecutive
     * @param string $consecutiveId
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($consecutiveId);

    /**
     * Retrieve consecutive matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \WolfSellers\Consecutive\Api\Data\ConsecutiveSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete consecutive
     * @param \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface $consecutive
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \WolfSellers\Consecutive\Api\Data\ConsecutiveInterface $consecutive
    );

    /**
     * Delete consecutive by ID
     * @param string $consecutiveId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($consecutiveId);
}

