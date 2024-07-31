<?php
/**
 * Copyright © WolfSellers All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\AmastyMegaMenu\Rewrite\Amasty\MegaMenuLite\Block;

class Container extends \Amasty\MegaMenuLite\Block\Container
{

    /**
     * @return int
     */
    protected function getCacheLifetime()
    {
        return 3600;
    }

    public function getCacheKey() {
        $cacheKey = parent::getCacheKey();
        $cacheKey = $cacheKey . "-" . ($this->getViewModel()->isMobile() ? "mobile" : "desktop");
        return $cacheKey;
    }

}

