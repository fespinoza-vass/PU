<?php


namespace WolfSellers\Bopis\Model\Email\Identity;


use Magento\Sales\Model\Order\Email\Container\IdentityInterface;

class CancelOrder extends \Magento\Sales\Model\Order\Email\Container\Container implements IdentityInterface
{
    const XML_PATH_EMAIL_COPY_TO = 'bopis/cancel_email/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD = 'bopis/cancel_email/copy_method';
    const XML_PATH_EMAIL_IDENTITY = 'bopis/cancel_email/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'bopis/cancel_email/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'bopis/cancel_email/template';
    const XML_PATH_EMAIL_ENABLED = 'bopis/cancel_email/enabled';

    /**
     * Is email enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }
        return false;
    }

    /**
     * Return copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * Return guest template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Return email identity
     *
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
