<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 11/08/2018
 * Time: 03:24 PM
 */

namespace PechoSolutions\Visanet\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
