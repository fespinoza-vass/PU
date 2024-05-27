<?php

namespace WolfSellers\Bopis\CustomerData;

use Magento\Checkout\Model\Session;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

/**
 *
 */
class Bopis implements SectionSourceInterface
{
    /**
     *
     */
    const DELIVERY_TITLE = "carriers/tablerate/title";
    /**
     * @var Session
     */
    private Session $checkoutSession;
    /**
     * @var BopisRepositoryInterface
     */
    private BopisRepositoryInterface $bopisRepository;
    /**
     * @var \WolfSellers\Bopis\Helper\Bopis
     */
    private \WolfSellers\Bopis\Helper\Bopis $bopis;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    /**
     * @var \WolfSellers\Bopis\Helper\Config
     */
    private \WolfSellers\Bopis\Helper\Config $config;
    /**
     * @var \Laminas\Log\Logger
     */
    protected $logger;

    /**
     * @param Session $checkoutSession
     * @param BopisRepositoryInterface $bopisRepository
     * @param \WolfSellers\Bopis\Helper\Bopis $bopis
     * @param ScopeConfigInterface $scopeConfig
     * @param \WolfSellers\Bopis\Helper\Config $config
     */
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

    /**
     * @return array
     */
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
