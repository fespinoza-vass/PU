<?php

namespace WolfSellers\Email\Model\Email\Identity;

use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\Email\Model\Email\Identity\Identity;

class SatisfactionSurvey extends Identity
{
    /** @var string */
    const XML_PATH_EMAIL_ENABLED = 'bopis/satisfaction_survey_email/enabled';

    /** @var string */
    const XML_PATH_EMAIL_TEMPLATE = 'bopis/satisfaction_survey_email/template';

    /** @var string */
    const XML_PATH_EMAIL_IDENTITY = 'bopis/satisfaction_survey_email/identity';

    /** @var string */
    const XML_PATH_AMASTY_FORM_ID = 'bopis/satisfaction_survey_email/amasty_form';

    /** @var string  */
    const URL_SATISFACTION_SURVEY = 'bopis/survey/satisfaction';

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

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getAmastyFormId(): mixed
    {
        return $this->getConfigValue(self::XML_PATH_AMASTY_FORM_ID);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSatisfactionSurveyUrl()
    {
        return $this->getStore()->getUrl(self::URL_SATISFACTION_SURVEY);
    }
}
