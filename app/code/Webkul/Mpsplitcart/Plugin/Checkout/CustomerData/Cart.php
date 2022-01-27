<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitcart
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsplitcart\Plugin\Checkout\CustomerData;

use Magento\Framework\Exception\LocalizedException;

/**
 * Cart source
 */
class Cart
{
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * afterGetSectionData
     * updates the result from checkout session
     */
    public function afterGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject,
        $result
    ) {
        if ($this->checkoutSession->getWkCustomQuote()) {
            return $this->checkoutSession->getWkCustomQuote();
        }
        return $result;
    }
}
