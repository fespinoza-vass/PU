<?php

namespace WolfSellers\ZipCode\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\ZipCode\Model\ZipCodeFactory;

class GetCps implements HttpGetActionInterface
{
    /**
     * @var ZipCodeFactory
     */
    protected ZipCodeFactory $model;

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
     * @param ZipCodeFactory $zipCodeFactory
     * @param JsonFactory $resultJsonFactory
     * @param Json $json
     * @param RequestInterface $request
     */
    public function __construct(
        ZipCodeFactory $zipCodeFactory,
        JsonFactory $resultJsonFactory,
        Json $json,
        RequestInterface $request
    ) {
        $this->model = $zipCodeFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->json = $json;
        $this->request = $request;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        if ($this->request->isAjax()) {
            $ciudad = $this->request->getParam('ciudad');
            $town = $this->request->getParam('town');

            $zip = $this->model->create();

            $collection = $zip->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('ciudad', ['like' => $ciudad])
                ->addFieldToFilter(
                    ['suburb', 'localidad'],
                    [['like' => '%' . $town . '%'], ['like' => '%' . $town . '%']]
                );

            return $this->resultJsonFactory->create()->setData($this->json->serialize($collection));
        }
    }
}
