<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-06
 * Time: 14:10
 */

declare(strict_types=1);

namespace WolfSellers\FastShipping\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface;

/**
 * Fast Shipping View.
 */
class FastShippingView extends View
{
    /** @var array */
    private array $regionOptions;

    /** @var CollectionFactory */
    private CollectionFactory $regionCollectionFactory;

    /**
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param CollectionFactory $regionCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory $regionCollection,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );

        $this->regionCollectionFactory = $regionCollection;
    }

    /**
     * Get region options list.
     *
     * @return array
     */
    public function getRegionOptions()
    {
        if (!isset($this->regionOptions)) {
            $this->regionOptions = $this->regionCollectionFactory->create()->addAllowedCountriesFilter(
                $this->_storeManager->getStore()->getId()
            )->toOptionArray();
        }

        return $this->regionOptions;
    }

    /**
     * Current ubigeo.
     *
     * @return string|null
     */
    public function getCurrentUbigeo(): ?string
    {
        return $this->customerSession->getFastShippingUbigeo();
    }

    /**
     * Last estimation.
     *
     * @return string|null
     */
    public function getLastEstimation(): ?string
    {
        $lastEstimation = $this->customerSession->getFastShippingEstimate() ?? [];
        $productId = (int) $this->getProduct()->getId();
        $estimation = null;

        if (isset($lastEstimation['productId']) && $lastEstimation['productId'] === $productId) {
            $estimation = $lastEstimation['dateFormat'];
        }

        return $estimation;
    }
}
