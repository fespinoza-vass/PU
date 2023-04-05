<?php
namespace WolfSellers\SkinCare\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SplitHelper extends AbstractHelper
{
    public const LIMIT = 22;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Construct Method
     *
     * @param Context $context
     * @param ScopeInterface $scopeConfig
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
        $strLimit = $this->getConfigValue("skincare/characters/limit") ?? self::LIMIT;
        if (strlen($productName) > $strLimit) {
            return substr($productName, 0, $strLimit). '...';
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
}
