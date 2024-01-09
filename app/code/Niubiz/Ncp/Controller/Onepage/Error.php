<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Niubiz\Ncp\Controller\Onepage;

class Error extends \Magento\Checkout\Controller\Onepage
{
    /**
     * Order success action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    { 
        $resultPage = $this->resultPageFactory->create(); 

        return $resultPage;
    }
}