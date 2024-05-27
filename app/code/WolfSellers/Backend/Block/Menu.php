<?php

namespace WolfSellers\Backend\Block;

class Menu extends \Magento\Backend\Block\Menu
{
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = [];
        try {
            $cacheKeyInfo = [
                'admin_top_nav',
                $this->getActive(),
                !empty($this->_authSession->getUser()->getId()) ? $this->_authSession->getUser()->getId() : 0,
                $this->_localeResolver->getLocale(),
            ];
            // Add additional key parameters if needed
            $newCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
            if (is_array($newCacheKeyInfo) && !empty($newCacheKeyInfo)) {
                $cacheKeyInfo = array_merge($cacheKeyInfo, $newCacheKeyInfo);
            }
        }catch (\Exception $exception){

        }
        return $cacheKeyInfo;
    }

}
