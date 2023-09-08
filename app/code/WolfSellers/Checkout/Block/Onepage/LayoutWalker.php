<?php

namespace WolfSellers\Checkout\Block\Onepage;

class LayoutWalker
{
    /**
     * @var array
     */
    private array $layoutArray;

    /**
     * Path templates
     *['customer-data-step']['children']['customer-fieldsets']['children']['customer-data-name']
     * @var array
     */
    public const LAYOUT_PATH_TEMPLATES = [
        '{CUSTOMER-FIELDSETS}' => '{CUSTOMER-DATA}.>>.customer-fieldsets.>>',
        '{CUSTOMER-DATA}' => '{CHECKOUT_STEPS}.>>.customer-data-step',
        '{GIFT_MESSAGE}' => '{SUMMARY}.>>.container-giftmessage',
        '{SUMMARY}' => '{SIDEBAR}.>>.summary',
        '{GIFT_WRAP}' => '{ADDITIONAL_STEP}.>>.checkboxes.>>.gift_wrap',
        '{SHIPPING_ADDRESS_FIELDSET}' => '{SHIPPING_ADDRESS}.>>.shipping-address-fieldset',
        '{SHIPPING_RATES_VALIDATION}' =>
            '{CHECKOUT}.>>.steps.>>.shipping-step.>>.step-config.>>.shipping-rates-validation',
        '{AMCHECKOUT_DELIVERY_DATE}' => '{CHECKOUT}.>>.steps.>>.shipping-step.>>.amcheckout-delivery-date',
        '{CHECKOUT_STEPS}' => '{CHECKOUT}.>>.steps',
        '{SHIPPING_ADDRESS}' => '{CHECKOUT}.>>.steps.>>.shipping-step.>>.shippingAddress',
        '{GIFT_MESSAGE_CONTAINER}' => '{ADDITIONAL_STEP}.>>.checkboxes.>>.gift_message_container',
        '{PAYMENT}' => '{BILLING_STEP}.>>.payment',
        '{CART_ITEMS}' => '{SIDEBAR}.>>.summary.>>.cart_items',
        '{BILLING_STEP}' => '{CHECKOUT}.>>.steps.>>.billing-step',
        '{ADDITIONAL_STEP}' => '{SIDEBAR}.>>.additional', //additional summary fields
        '{SIDEBAR}' => '{CHECKOUT}.>>.sidebar',
        '{CHECKOUT}' => 'components.checkout',
        '{PROVIDER}' => 'components.checkoutProvider'
    ];

    public const ESCAPED_SEPARATOR = '\dot/';

    public function __construct(array $layoutArray)
    {
        $this->layoutArray = $layoutArray;
    }

    /**
     * isset
     * @param $path
     * @return bool
     */
    public function isExist($path): bool
    {
        $path = $this->parseArrayPath($path);

        return $this->issetWalker($this->layoutArray, $path);
    }

    /**
     * @param string $path
     * @param $value
     * @return $this
     */
    public function setValue(string $path, $value): LayoutWalker
    {
        if ($path === '') {
            $this->layoutArray = $value;
            return $this;
        }
        $path = $this->parseArrayPath($path);
        $this->arrayWalker($this->layoutArray, $path, $value);

        return $this;
    }

    /**
     * @param $path
     * @return array|bool|float|int|string|null
     */
    public function getValue($path)
    {
        if ($path === '') {
            return $this->layoutArray;
        }
        $path = $this->parseArrayPath($path);

        return $this->getWalker($this->layoutArray, $path);
    }

    /**
     * unset
     * @param $path
     * @return $this
     */
    public function unsetByPath($path): LayoutWalker
    {
        if ($path === '') {
            unset($this->layoutArray);
            return $this;
        }
        $path = $this->parseArrayPath($path);
        $this->unsetWalker($this->layoutArray, $path);

        return $this;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->layoutArray;
    }

    /**
     * @param string $keyPath
     * @return array
     */
    public function parseArrayPath(string $keyPath): array
    {
        $keyPath = preg_replace('/[\s\n\r]/', '', $keyPath);
        $keyPath = str_replace(
            array_keys(self::LAYOUT_PATH_TEMPLATES),
            array_values(self::LAYOUT_PATH_TEMPLATES),
            $keyPath
        );
        $keyPath = str_replace('>>', 'children', $keyPath);
        $keyArray = explode('.', $keyPath);

        foreach ($keyArray as &$key) {
            $key = str_replace(self::ESCAPED_SEPARATOR, '.', $key);
        }

        return $keyArray;
    }

    /**
     * @param array $haystack
     * @param array $path
     * @param string|int|float|bool|array|null $value
     */
    protected function arrayWalker(array &$haystack, array $path, $value)
    {
        $currentKey = array_shift($path);
        if (!isset($haystack[$currentKey])) {
            $haystack[$currentKey] = [];
        }
        if (empty($path)) {
            //end of path, walk completed
            $haystack[$currentKey] = $value;
            return;
        }

        $this->arrayWalker($haystack[$currentKey], $path, $value);
    }

    /**
     * @param array $haystack
     * @param array $path
     */
    protected function unsetWalker(array &$haystack, array $path)
    {
        $currentKey = array_shift($path);
        if (!array_key_exists($currentKey, $haystack)) {
            return;
        }

        if (empty($path)) {
            //end of path, walk completed
            unset($haystack[$currentKey]);
            return;
        }

        $this->unsetWalker($haystack[$currentKey], $path);
    }

    /**
     * @param array $haystack
     * @param array $path
     *
     * @return bool
     */
    protected function issetWalker(array &$haystack, array $path): bool
    {
        $currentKey = array_shift($path);
        if (!isset($haystack[$currentKey])) {
            return false;
        }

        if (empty($path)) {
            //end of path, walk completed
            return true;
        }

        return $this->issetWalker($haystack[$currentKey], $path);
    }

    /**
     * @param array $haystack
     * @param array $path
     * @return mixed|null
     */
    protected function getWalker(array &$haystack, array $path)
    {
        $currentKey = array_shift($path);
        if (!isset($haystack[$currentKey])) {
            return null;
        }

        if (empty($path)) {
            //end of path, walk completed
            return $haystack[$currentKey];
        }

        return $this->getWalker($haystack[$currentKey], $path);
    }
}
