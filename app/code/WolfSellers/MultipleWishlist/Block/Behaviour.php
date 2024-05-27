<?php

namespace WolfSellers\MultipleWishlist\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\MultipleWishlist\Helper\Data;
use Magento\Framework\Registry;
use Magento\Wishlist\Model\Wishlist;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\MultipleWishlist\CustomerData\MultipleWishlist;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use WolfSellers\MultipleWishlist\Logger\Logger;

class Behaviour extends \Magento\Framework\View\Element\Template
{
    /**
     * Wishlist data
     *
     * @var Data|null
     */
    protected $_wishlistData = null;

    /**
     * @var Registry
     */
    private Registry $_registry;

    /**
     * @var CustomerSession
     */
    private CustomerSession $_customerSession;

    /**
     * @var Wishlist
     */
    private Wishlist $_wishlist;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var MultipleWishlist
     */
    private MultipleWishlist $_multipleWishlist;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $wishlistItemCollectionFactory;

    /**
     * @param Context $context
     * @param Data $wishlistData
     * @param Registry $registry
     * @param Wishlist $wishlist
     * @param CustomerSession $session
     * @param MultipleWishlist $multipleWishlist
     * @param Logger $logger
     * @param CollectionFactory $wishlistItemCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context           $context,
        Data              $wishlistData,
        Registry          $registry,
        Wishlist          $wishlist,
        CustomerSession   $session,
        MultipleWishlist  $multipleWishlist,
        Logger            $logger,
        CollectionFactory $wishlistItemCollectionFactory,
        array             $data = []
    )
    {
        $this->_wishlistData = $wishlistData;
        $this->_registry = $registry;
        $this->_wishlist = $wishlist;
        $this->_customerSession = $session;
        $this->_multipleWishlist = $multipleWishlist;
        $this->logger = $logger;
        $this->wishlistItemCollectionFactory = $wishlistItemCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Wishlist creation url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl(
            'wishlist/index/createwishlist',
            [
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }

    /**
     * Render block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct(): mixed
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Returns true if the specific product ID is in any customer's wishlist.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isInAnyWishlist()
    {
        try {
            if (!$this->_customerSession->isLoggedIn()) {
                $this->logger->info('No customer is logged in');
                return false;
            }

            $product = $this->getCurrentProduct();
            if (!$product) {
                $this->logger->info('There is no product specified');
                return false;
            }

            $inInAnyWishlist = false;
            $customerId = $this->_customerSession->getCustomer()->getId();

            if ($this->_wishlistData->isMultipleEnabled()) {
                $this->logger->info('multipleWishlist');
                $inInAnyWishlist = $this->getProductInMultipleWishlist($product->getId(), $customerId);
            } else {
                $wishlist = $this->_wishlist->loadByCustomerId($customerId, false);
                $wishlistItem = $wishlist->getItemCollection()->addFieldToFilter('main_table.product_id', $product->getId())->getFirstItem();

                if ($wishlistItem->getId()) {
                    $this->logger->info('There is simple WishList');
                    $inInAnyWishlist = true;
                }
            }

            $this->logger->info('Wishlist data', [
                'customer' => $customerId,
                'product' => $product->getId(),
                'inInAnyWishlist' => $inInAnyWishlist
            ]);

            return $inInAnyWishlist;
        } catch (\Throwable $err) {
            $this->logger->error($err->getMessage());
            return false;
        }

    }

    /**
     * Search for a product in the customer's multiple wish list
     *
     * @param $productId
     * @param $customerId
     * @return bool
     */
    private function getProductInMultipleWishlist($productId, $customerId)
    {
        $data = $this->_multipleWishlist->getSectionData();
        if (!isset($data['short_list'])) return false;

        foreach ($data['short_list'] as $list) {
            $wishId = $list['id'];

            $wish = $this->_wishlist->load($wishId);

            $wishlistItemCollection = $this->wishlistItemCollectionFactory->create()->addWishlistFilter($wish);

            $wishlistItems = $wishlistItemCollection->getItems();

            foreach ($wishlistItems as $wishlistItemId => $wishlistItem) {
                if ($wishlistItem->getProductId() == $productId) {
                    return true;
                }
            }
        }

        return false;
    }
}
