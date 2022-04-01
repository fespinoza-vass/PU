<?php

namespace WolfSellers\ZipCode\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class GetCps extends Action
{
    /** @var $model \WolfSellers\ZipCode\Model\ZipCodeFactory */
    protected $model;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    public function __construct(
        Context $context,
        \WolfSellers\ZipCode\Model\ZipCodeFactory $zipCodeFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        parent::__construct($context);
        $this->model = $zipCodeFactory;
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
        if ($this->getRequest()->isAjax()) {
            $ciudad = $this->getRequest()->getParam('ciudad');
            $town = $this->getRequest()->getParam('town');

            $zip = $this->model->create();
            $collection = $zip->getCollection()->addFieldToSelect('*')
                ->addFieldToFilter('ciudad',['like' => $ciudad])
                ->addFieldToFilter(['suburb','localidad'],[['like'=>'%'.$town.'%'],['like'=>'%'.$town.'%']]);

            return $this->resultJsonFactory->create()->setData($this->jsonHelper->jsonEncode($collection));
        }
    }
}
