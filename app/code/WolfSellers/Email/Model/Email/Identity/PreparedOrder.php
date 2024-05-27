<?php

namespace WolfSellers\Email\Model\Email\Identity;

use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\Email\Model\Email\Identity\Identity;

class PreparedOrder extends Identity
{
    /** @var string */
    const XML_PATH_EMAIL_ENABLED = 'bopis/prepared_email/enabled';

    /** @var string */
    const XML_PATH_EMAIL_TEMPLATE = 'bopis/prepared_email/template';

    /** @var string */
    const XML_PATH_EMAIL_IDENTITY = 'bopis/prepared_email/identity';

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->isEmailEnabled(self::XML_PATH_EMAIL_ENABLED);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getTemplateId(): mixed
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getEmailIdentity(): mixed
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY);
    }
}
