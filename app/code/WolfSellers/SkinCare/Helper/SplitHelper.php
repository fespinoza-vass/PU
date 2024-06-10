<?php
namespace WolfSellers\SkinCare\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use phpseclib3\Crypt\DH\Parameters;

class SplitHelper extends AbstractHelper
{
    public const FIRST_LINE = 8;

    public const SECOND_LINE = 8;

    public const URL_PLUGIN_SDK = 'skincare/characters/url_plugin_sdk';
    public const URL_PLUGIN_PARAM = 'skincare/characters/url_plugin_param';
    public const URL_PLUGIN_KEY = 'skincare/characters/url_plugin_key';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Construct Method
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Context $context, ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Methodo to explode product name and
     *
     * @param mixed $productName
     * @return string
     */
    public function splitName($productName)
    {
        $firstLineLimit = $this->getConfigValue("skincare/characters/first_line") ?? self::FIRST_LINE;
        $secondLineLimit = $this->getConfigValue("skincare/characters/second_line") ?? self::SECOND_LINE;
        if (strlen($productName) > ($secondLineLimit + $firstLineLimit)) {
            $productName = trim(ucfirst(strtolower($productName)));
            $firstLineName =  substr($productName, 0, $firstLineLimit) ;

            $secondLineName = trim(str_replace($firstLineName, '', $productName));
            $secondLineName =  substr($secondLineName, 0, $secondLineLimit).'...' ;
            return '<span style="display:block">' .$firstLineName .'</span>' .'<span style="display:block">' . $secondLineName .'</span>';
        }
        return $productName;
    }

    /**
     * Method to get configurable options
     *
     * @param mixed $path
     * @return mixed
     */
    private function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * @return string
     */
    public function getUrlPlugin()
    {

        $url = $this->scopeConfig->getValue(self::URL_PLUGIN_SDK,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $param = $this->scopeConfig->getValue(self::URL_PLUGIN_PARAM, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $key = $this->scopeConfig->getValue(self::URL_PLUGIN_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        // s.src = 'https://plugins-media.makeupar.com/c41059/sdk.js?apiKey=' + k;

        $url_plugin = $url . $param . '/sdk.js?apiKey=' . $key;

        return $url_plugin;
    }
}
