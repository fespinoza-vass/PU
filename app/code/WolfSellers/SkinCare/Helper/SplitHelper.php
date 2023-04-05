<?php
namespace WolfSellers\SkinCare\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SplitHelper extends AbstractHelper
{
    public const FIRST_LINE = 8;

    public const SECOND_LINE = 8;

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
            $firstLineName =  '<p>' .substr($productName, 0, $firstLineLimit) .'</p>';

            $secondLineName = trim(str_replace($firstLineName, '', $productName));
            $secondLineName =  '<p>' .substr($secondLineName, 0, $secondLineLimit).'...' .'</p>';
            return $firstLineName + $secondLineName;
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
