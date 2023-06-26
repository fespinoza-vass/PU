<?php

namespace WolfSellers\Bopis\CustomerData;

use Magento\Checkout\Model\Session;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class Bopis implements SectionSourceInterface
{
    const DELIVERY_TITLE = "carriers/tablerate/title";
    private Session $checkoutSession;
    private BopisRepositoryInterface $bopisRepository;
    private \WolfSellers\Bopis\Helper\Bopis $bopis;
    private ScopeConfigInterface $scopeConfig;
    private \WolfSellers\Bopis\Helper\Config $config;
    protected $logger;

    public function __construct(
        Session $checkoutSession,
        BopisRepositoryInterface $bopisRepository,
        \WolfSellers\Bopis\Helper\Bopis $bopis,
        ScopeConfigInterface $scopeConfig,
        \WolfSellers\Bopis\Helper\Config $config
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->bopisRepository = $bopisRepository;
        $this->bopis = $bopis;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/bopis.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    public function getSectionData()
    {
        try {
            $bopis = $this->bopisRepository->getByQuoteId($this->checkoutSession->getQuoteId());
        } catch (LocalizedException $e) {
            return [
                "error" => $e->getMessage(),
                "is_active" => (bool) $this->config->isActive()
            ];

        }

        return [
            "formatted" => $bopis->getAddressFormatted(),
            "object" => $bopis->getAddressObject(),
            "store" => $bopis->getStore(),
            "type" => $bopis->getType(),
            "can_buy" => $this->checkoutSession->getCanBuy(),
            "delivery_copy" => $this->scopeConfig->getValue(self::DELIVERY_TITLE),
            "is_active" => (bool) $this->config->isActive()
        ];
    }

}
