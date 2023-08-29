<?php

namespace WolfSellers\Bopis\Plugin\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\ShippingMethodManagement;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class ShippingMethodManagementPlugin
{
    const BOPIS_CODE = 'bopis';
    private BopisRepositoryInterface $bopisRepository;

    public function __construct(
        BopisRepositoryInterface $bopisRepository
    )
    {
        $this->bopisRepository = $bopisRepository;
    }

    public function afterEstimateByExtendedAddress(
        ShippingMethodManagement $subject,
        $result,
        $cartId
    ) {
        if (!$this->isPickup($cartId)){
            return $result;
        }
        foreach ($result as $i => $method) {
            if ($method->getMethodCode() !== self::BOPIS_CODE){
                unset($result[$i]);
            }
        }
        return $result;
    }

    public function afterEstimateByAddressId(
        ShippingMethodManagement $subject,
        $result,
        $cartId
    ) {
        if (!$this->isPickup($cartId)){
            return $result;
        }
        foreach ($result as $i => $method) {
            if ($method->getMethodCode() !== self::BOPIS_CODE){
                unset($result[$i]);
            }
        }
        return $result;
    }

    private function isPickup($cartId){
        try {
            $bopis = $this->bopisRepository->getByQuoteId($cartId);
            return $bopis->getType() == 'store-pickup';
        } catch (LocalizedException $e) {
            return false;
        }
    }

}
