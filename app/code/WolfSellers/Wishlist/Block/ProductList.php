<?php

namespace WolfSellers\Wishlist\Block;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Pricing\Render;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;

class ProductList extends \Magento\Catalog\Block\Product\AbstractProduct  #\Magento\Framework\View\Element\Template
{
    protected $logger;
    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    private \Magento\Wishlist\Model\WishlistFactory $wishlistFactory;
    private ItemFactory $itemFactory;
    private Context $context;
    private \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param Data $urlHelper
     * @param ProductFactory $productloader
     * @param FormKey $formKey
     * @param WishlistFactory $wishlistFactory
     * @param ItemFactory $itemFactory
     * @param CollectionFactory $productCollectionFactory
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context                         $context,
        \Magento\Framework\Url\Helper\Data                             $urlHelper,
        \Magento\Catalog\Model\ProductFactory                          $productloader,
        \Magento\Framework\Data\Form\FormKey                           $formKey,
        \Magento\Wishlist\Model\WishlistFactory                        $wishlistFactory,
        \Magento\Wishlist\Model\ItemFactory                            $itemFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Session                                                        $checkoutSession,
        array                                                          $data = []
    )
    {
        $this->urlHelper = $urlHelper;
        $this->productFactory = $productloader;
        $this->formKey = $formKey;
        parent::__construct($context, $data);
        $this->wishlistFactory = $wishlistFactory;
        $this->itemFactory = $itemFactory;
        $this->context = $context;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->checkoutSession = $checkoutSession;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/wishlist.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Load Products collection
     *
     * @return Product array
     */
    public function getLoadProducts()
    {
        /** @var Collection $wishlists */
        $wishlists = $this->wishlistFactory->create()->getCollection()
            ->addFieldToFilter('save_for_later', array('eq' => '1'));
        $this->logger->info("\WolfSellers\Wishlist\Block\ProductList::getLoadProducts");
        $ids = [];
        if ($wishlists->count()) {
            $wishlist = $wishlists->getFirstItem();
            $items = $this->getItems($wishlist);
            /** @var \Magento\Wishlist\Model\Item $item */
            foreach ($items as $item) {
                $ids[] = $item->getProduct()->getId();
            }
        }
        $this->logger->info(var_export($ids, true));
        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect(["name", "price", "image"])
            ->addAttributeToFilter("entity_id", ['in' => $ids]);

        return $products;
    }


    /**
     * @param $wishlist
     * @return array
     */
    private function getItems($wishlist): array
    {
        return $this->itemFactory->create()->getCollection()->addFieldToFilter("wishlist_id", $wishlist->getData("wishlist_id") ?? $wishlist->getStoredData("wishlist_id"))->getItems();
    }

    /**
     * Load Product
     *
     * @return Product array
     */
    public function getLoadProduct($id)
    {
        return $this->productFactory->create()->load($id);
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product, ['_escape' => false]);
        return [
            'action' => $url,
            'data' => [
                'product' => (int)$product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * Get product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender($product);
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }
        return $price;
    }

    /**
     * Get price render
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return Render
     */
    protected function getPriceRender($product)
    {
        return $this->getLayout()->createBlock(\Magento\Framework\Pricing\Render::class, "product.price.render.default" . $product->getSku())
            ->setData('is_product_list', true);
    }

    /**
     * return order
     *
     * @return Order
     */
    public function getOrder() {
        return $this->checkoutSession->getLastRealOrder();
    }

}
