<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Validator;

class DiscountDescription
{
    /**
     * @var Validator
     */
    private $validator;

    public function __construct(
        Validator $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * @param Address $address
     * @param int|float $pointsUsed
     * @return $this
     */
    public function addRewardsDescription(Address $address, $pointsUsed): self
    {
        if ($pointsUsed > 0) {
            $description = $address->getDiscountDescriptionArray();
            $description['amrewards'] = __('Used %1 reward points', $pointsUsed);

            $address->setDiscountDescriptionArray($description);
            $this->validator->prepareDescription($address);
        }

        return $this;
    }
}
