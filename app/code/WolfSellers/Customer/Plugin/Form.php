<?php
namespace WolfSellers\Customer\Plugin;

use \Magento\Framework\App\Request\Http;

class Form
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
     * @param array $result
     * @return bool
     */
    public function afterGetUserDefinedAttributes(\Magento\CustomAttributeManagement\Block\Form $subject, $result)
    {
        $moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        $route      = $this->request->getRouteName();
        
        if ($moduleName == 'customer') {
            foreach ($result as $code => $attribute) {
                if ($code == "telefono") {
                    
                    $result[$attribute->getAttributeCode()]->setData('is_required',1);
                    $result[$attribute->getAttributeCode()]->setData('scope_is_required',1);
                    $result[$attribute->getAttributeCode()]->setData('validate_rules','{"min_text_length":"7","max_text_length":"12","input_validation":"numeric"}');
                }
            }
        }
        return $result;
        
    }

}
