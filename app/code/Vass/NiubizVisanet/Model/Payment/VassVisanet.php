<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vass\NiubizVisanet\Model\Payment;

class VassVisanet extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "vassvisanet";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}

