<?php

namespace WolfSellers\Bopis\Plugin\Model\Customer;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Address;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class AddressPlugin
{
    private Session $checkoutSession;
    private BopisRepositoryInterface $bopisRepository;

    public function __construct(
        Session $checkoutSession,
        BopisRepositoryInterface $bopisRepository
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->bopisRepository = $bopisRepository;
    }

    public function afterUpdateData(
        Address $subject,
        $result
    ) {
        $quoteId = $this->checkoutSession->getQuoteId();
        try{
            $bopis = $this->bopisRepository->getByQuoteId($quoteId);
            if ($bopis->getType() == "store-pickup"){
                $result->setIsDefaultShipping(false);
            }
        }catch (Exception $e){}

        return $result;
    }
}
