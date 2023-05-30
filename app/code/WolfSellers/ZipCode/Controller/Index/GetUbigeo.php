<?php

namespace WolfSellers\ZipCode\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\Urbano\Helper\Ubigeo;

/**
 * Get Town.
 */
class GetUbigeo implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var Ubigeo
     */
    protected Ubigeo $ubigeoHelper;

    public function __construct(
        JsonFactory $resultJsonFactory,
        Json $json,
        Ubigeo $ubigeoHelper
    ) {
        $this->ubigeoHelper = $ubigeoHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->json = $json;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $ubigeo = $this->request->getParam('ubigeo');
        $data = $this->ubigeoHelper->getDays($ubigeo);

        return $this->resultJsonFactory->create()->setData($this->json->serialize($data));
    }
}
