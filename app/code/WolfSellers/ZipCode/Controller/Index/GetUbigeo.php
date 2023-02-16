<?php

namespace WolfSellers\ZipCode\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data;
use WolfSellers\Urbano\Helper\Ubigeo;
/**
 * Get Town.
 */
class GetUbigeo extends Action
{
    private JsonFactory $resultJsonFactory;
    private Data $jsonHelper;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $jsonHelper,
        Ubigeo $ubigeoHelper
    ) {
        parent::__construct($context);
        $this->ubigeoHelper = $ubigeoHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $ubigeo = $this->getRequest()->getParam('ubigeo');
        $data = $this->ubigeoHelper->getDays($ubigeo);
        return $this->resultJsonFactory->create()->setData($this->jsonHelper->jsonEncode($data));
    }
}
