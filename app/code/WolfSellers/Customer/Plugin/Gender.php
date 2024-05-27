<?php
namespace WolfSellers\Customer\Plugin;

use \Magento\Framework\App\Request\Http;

class Gender
{
    /**
     * @var Http
     */
    protected $request;

    public function __construct(
        Http $request
    ){
        $this->request = $request;
    }
    /**
     * Check if dob attribute marked as required
     *
     * @param \Magento\Customer\Block\Widget\Dob $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsRequired(\Magento\Customer\Block\Widget\Gender $subject, $result)
    {
        $moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        $route      = $this->request->getRouteName();
        
        if ($moduleName == 'customer') {
            return true;
        }
        return false;
        
    }
}
