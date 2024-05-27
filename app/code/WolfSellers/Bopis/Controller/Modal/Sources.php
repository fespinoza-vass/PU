<?php

namespace WolfSellers\Bopis\Controller\Modal;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use WolfSellers\Bopis\Helper\Bopis;

class Sources implements HttpGetActionInterface {

    private Bopis $bopisHelper;

    /**
     * @param Bopis $bopisHelper
     */
    public function __construct(
        Bopis $bopisHelper
    ) {
        $this->bopisHelper = $bopisHelper;
    }

    /**
     * @return Json
     */
    public function execute(): Json {
        return $this->bopisHelper->getSources(true);
    }
}
