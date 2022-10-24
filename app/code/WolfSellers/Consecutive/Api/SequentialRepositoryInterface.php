<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SequentialRepositoryInterface
{

    /**
     * Save Sequential
     * @param \WolfSellers\Consecutive\Api\Data\SequentialInterface $sequential
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \WolfSellers\Consecutive\Api\Data\SequentialInterface $sequential
    );

    /**
     * Retrieve Sequential
     * @param string $sequentialId
     * @return \WolfSellers\Consecutive\Api\Data\SequentialInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($sequentialId);

    /**
     * Retrieve Sequential matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \WolfSellers\Consecutive\Api\Data\SequentialSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Sequential
     * @param \WolfSellers\Consecutive\Api\Data\SequentialInterface $sequential
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \WolfSellers\Consecutive\Api\Data\SequentialInterface $sequential
    );

    /**
     * Delete Sequential by ID
     * @param string $sequentialId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($sequentialId);
}

