<?php

declare(strict_types=1);

namespace WolfSellers\HeaderLinks\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    /** @var string */
    const CONFIG_PATH_ENABLED = 'wolfsellers_headerlinks/general/enabled';

    /** @var string  */
    const CONFIG_PATH_STICKY = 'wolfsellers_headerlinks/general/sticky';

    /** @var string */
    const CONFIG_PATH_BUTTON_1_VISIBLE = 'wolfsellers_headerlinks/btn1/button1';

    /** @var string */
    const CONFIG_PATH_BUTTON_1_NAME = 'wolfsellers_headerlinks/btn1/button1_name';

    /** @var string */
    const CONFIG_PATH_BUTTON_1_URL = 'wolfsellers_headerlinks/btn1/button1_url';

    /** @var string */
    const CONFIG_PATH_BUTTON_1_CONTENT = 'wolfsellers_headerlinks/btn1/button1_content';

    /** @var string */
    const CONFIG_PATH_BUTTON_2_VISIBLE = 'wolfsellers_headerlinks/btn2/button2';

    /** @var string */
    const CONFIG_PATH_BUTTON_2_NAME = 'wolfsellers_headerlinks/btn2/button2_name';

    /** @var string */
    const CONFIG_PATH_BUTTON_2_URL = 'wolfsellers_headerlinks/btn2/button2_url';

    /** @var string */
    const CONFIG_PATH_BUTTON_2_CONTENT = 'wolfsellers_headerlinks/btn2/button2_content';

    /** @var string */
    const CONFIG_PATH_BUTTON_3_VISIBLE = 'wolfsellers_headerlinks/btn3/button3';

    /** @var string */
    const CONFIG_PATH_BUTTON_3_NAME = 'wolfsellers_headerlinks/btn3/button3_name';

    /** @var string */
    const CONFIG_PATH_BUTTON_3_URL = 'wolfsellers_headerlinks/btn3/button1_url';

    /** @var string */
    const CONFIG_PATH_BUTTON_3_CONTENT = 'wolfsellers_headerlinks/btn3/button3_content';

    /**
     * @param ScopeConfigInterface $_scopeConfig
     */
    public function __construct(
        protected ScopeConfigInterface $_scopeConfig
    )
    {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function keepSticky(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::CONFIG_PATH_STICKY);
    }

    /**
     * @param string $nameFunction
     * @return mixed
     */
    public function call(string $nameFunction)
    {
        return $this->$nameFunction();
    }

    /**
     * @return bool
     */
    public function isButton1Visible(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::CONFIG_PATH_BUTTON_1_VISIBLE);
    }

    /**
     * @return mixed
     */
    public function getButton1Name()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_1_NAME);
    }

    /**
     * @return mixed
     */
    public function getButton1Url()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_1_URL);
    }

    /**
     * @return mixed
     */
    public function getButton1Content()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_1_CONTENT);
    }

    /**
     * @return bool
     */
    public function isButton2Visible(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::CONFIG_PATH_BUTTON_2_VISIBLE);
    }

    /**
     * @return mixed
     */
    public function getButton2Name()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_2_NAME);
    }

    /**
     * @return mixed
     */
    public function getButton2Url()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_2_URL);
    }

    /**
     * @return mixed
     */
    public function getButton2Content()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_2_CONTENT);
    }

    /**
     * @return bool
     */
    public function isButton3Visible(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::CONFIG_PATH_BUTTON_3_VISIBLE);
    }

    /**
     * @return mixed
     */
    public function getButton3Name()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_3_NAME);
    }

    /**
     * @return mixed
     */
    public function getButton3Url()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_3_URL);
    }

    /**
     * @return mixed
     */
    public function getButton3Content()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_BUTTON_3_CONTENT);
    }
}
