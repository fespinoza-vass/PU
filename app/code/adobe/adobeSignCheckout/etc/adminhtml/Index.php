protected function _isAllowed()
{
return $this->_authorization->isAllowed('Magento_Store::manage');
}
