<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\Bopis\Api\Data\BopisInterface;
use WolfSellers\Bopis\Api\Data\BopisSearchResultsInterface;

interface BopisRepositoryInterface
{

    /**
     * Save BOPIS
     * @param BopisInterface $bopis
     * @return BopisInterface
     * @throws LocalizedException
     */
    public function save(BopisInterface $bopis);

    /**
     * Retrieve BOPIS
     * @param string $id
     * @return BopisInterface
     * @throws LocalizedException
     */
    public function get($id);

    /**
     * Retrieve BOPIS
     * @param string $quoteId
     * @return BopisInterface
     * @throws LocalizedException
     */
    public function getByQuoteId($quoteId);

    /**
     * Retrieve BOPIS matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return BopisSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete BOPIS
     * @param BopisInterface $cart
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        BopisInterface $bopis
    );

    /**
     * Delete BOPIS by ID
     * @param string $quoteId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($quoteId);
}

