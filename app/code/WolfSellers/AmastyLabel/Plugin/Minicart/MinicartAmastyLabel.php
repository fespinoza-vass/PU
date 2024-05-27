<?php

namespace WolfSellers\AmastyLabel\Plugin\Minicart;

use Magento\Checkout\CustomerData\DefaultItem;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\ProductFactory;
use Amasty\Label\Model\LabelViewer;
use Amasty\Label\Model\ResourceModel\Label\Collection;

class MinicartAmastyLabel
{
    /**
     * @param LabelViewer $labelViewer
     * @param ProductFactory $productRepository
     */
    public function __construct(
        protected LabelViewer $labelViewer,
        protected ProductFactory $productRepository
    )
    {
    }

    /**
     * @param DefaultItem $subject
     * @param $result
     * @param Item $item
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function afterGetItemData(DefaultItem $subject, $result, Item $item)
    {
        $product = $this->productRepository->create()->load($result['product_id']);
        $labels = $this->labelViewer->renderProductLabel($product, Collection::MODE_PDP);
        $result['amastyLabels'] = $labels ?? '';

        return $result;
    }
}
