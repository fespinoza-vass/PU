<?php

declare(strict_types=1);

namespace WolfSellers\Checkout\Block\Onepage;

use WolfSellers\Checkout\Block\Onepage\LayoutWalker;
use WolfSellers\Checkout\Block\Onepage\LayoutWalkerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface as Config;
use WolfSellers\Checkout\Model\Messages;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\GiftMessage\Model\Message;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\GiftMessage\Helper\Message as MessageHelper;

/**
 * Additional Layout processor with all private and dynamic data
 */
class GiftWrapProcessor implements LayoutProcessorInterface
{
    /**
     * @var Messages
     */
    private $giftMessages;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LayoutWalker
     */
    private $walker;

    /**
     * @var LayoutWalkerFactory
     */
    private $walkerFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * xpath prefix of module (section)
     * @var string '{section}/'
     */
    protected $pathPrefix = '/';
    /**
     * @var MessageHelper
     */
    protected $messageHelper;

    /**
     * Stored values by scopes
     *
     * @var array
     */
    protected $data = [];
    public function __construct(
        Messages $giftMessages,
        Config $scopeConfig,
        LayoutWalkerFactory $walkerFactory,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        CheckoutSession $checkoutSession,
        MessageHelper $messageHelper
    ) {

        $this->giftMessages = $giftMessages;
        $this->scopeConfig = $scopeConfig;
        $this->walkerFactory = $walkerFactory;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->checkoutSession = $checkoutSession;
        $this->messageHelper = $messageHelper;
    }

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     * @param $path
     * @param $storeId
     * @return mixed
     */
    protected function getValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            $storeId,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Gift Wrap and Gift Messages processor
     * @throws NoSuchEntityException
     */
    public function process($jsLayout): array
    {
        $quote = $this->checkoutSession->getQuote();
        $store = $this->storeManager->getStore();
        if (!$this->messageHelper->isMessagesAllowed('order_item', $quote, $store)) {
            return $jsLayout;
        }
        $this->walker = $this->walkerFactory->create(['layoutArray' => $jsLayout]);

        $this->processGiftMessage();

        return $this->walker->getResult();
    }

    /**
     * Gift Messages processor
     *
     * @throws NoSuchEntityException
     */
    public function processGiftMessage(): void
    {
        if (empty($messages = $this->giftMessages->getGiftMessages())) {
            $this->walker->unsetByPath('{GIFT_MESSAGE}');
        } else {

            $giftMessageContainer = [
                'component' => 'WolfSellers_Checkout/js/view/container-giftmessage',
                'displayArea' => 'container-giftmessage'
            ];
            $this->walker->setValue('{SUMMARY}.>>.container-giftmessage', $giftMessageContainer);
            $giftOptionsComponent = [
                'component' => 'WolfSellers_Checkout/js/view/gift-message',
                'displayArea' => 'gift_options',//==quote_message
                'config' => [
                    'template' => 'WolfSellers_Checkout/form/gift_message',
                    'popUpForm' => [
                        'options' => [
                            'buttons' => [
                                'save' => [
                                    'text' => 'Update',
                                    'class' => 'action primary action-save-address'
                                ],
                                'cancel' => [
                                    'text' => 'Cancel',
                                    'class' => 'action secondary action-hide-popup'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $this->walker->setValue('{GIFT_MESSAGE}.>>.gift_options', $giftOptionsComponent);

            $checkboxComponent = [
                'component' => 'WolfSellers_Checkout/js/view/checkbox-gift',
                'displayArea' => 'checkbox',
                'description' => '¿Tu compra es un regalo?',
                'dataScope' => 'checkout.gift_message',
                'value' => '1'
            ];
            $this->walker->setValue('{GIFT_MESSAGE}.>>.checkbox', $checkboxComponent);
            $itemMessage = $quoteMessage = [
                'component' => 'uiComponent',
                'children' => [],
            ];
            $checked = false;

            /** @var Message $message */
            foreach ($messages as $key => $message) {
                if ($message->getId()) {
                    $checked = true;
                }

                $node = $message
                    ->setData('item_id', $key)
                    ->toArray(['item_id', 'sender', 'recipient', 'message', 'title']);

                $node['component'] = 'WolfSellers_Checkout/js/view/gift-message';
                if ($key) {
                    $itemMessage['children'][] = $node;
                } else {
                    $quoteMessage['children'][] = $node;
                }
            }
            $this->walker->setValue(
                '{GIFT_MESSAGE}.config.popUpForm.options.messages',
                $this->translateTextForCheckout()
            );


            $this->walker->setValue('{GIFT_MESSAGE}.>>.checkbox.checked', $checked);
            $this->walker->setValue('{GIFT_MESSAGE}.>>.gift_options', $quoteMessage);
        }
    }

    /**
     * @return array
     */
    public function translateTextForCheckout(): array
    {
        $messages['gift'] = __('Gift messages has been successfully updated')->render();
        $messages['update'] = __('Update')->render();
        $messages['close'] = __('Close')->render();

        return $messages;
    }
}
