<?php

namespace WolfSellers\Bopis\Model\Email\Identity;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class SatisfactionSurvey
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
     * @param ScopeConfigInterface $_scopeConfig
     * @param StoreManagerInterface $_storeManager
     */
    public function __construct(
        protected ScopeConfigInterface $_scopeConfig,
        protected StoreManagerInterface $_storeManager
    ) {
    }

    /**
     * @param $path
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfigValue($path): mixed
    {
        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
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
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSatisfactionSurveyUrl()
    {
        return $this->_storeManager->getStore()->getUrl(self::URL_SATISFACTION_SURVEY);
    }
}
