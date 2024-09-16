<?php
namespace Izipay\Core\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Izipay\Core\Model\NotificationFactory;
use \Izipay\Core\Helper\Data;
use \Izipay\Core\Logger\Logger;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Notification extends Action implements CsrfAwareActionInterface
{
	/** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $_jsonResultFactory;
    protected $_notificationFactory;
    protected $_helper;
    protected $_logger;
    protected $_date;

    public function __construct(
        NotificationFactory $_notificationFactory,
        JsonFactory $_jsonResultFactory,
        Context $context,
        DateTime $_date,
        Data $_helper,
        Logger $_logger
    ) {
        parent::__construct($context);
        $this->_notificationFactory = $_notificationFactory;
        $this->_jsonResultFactory = $_jsonResultFactory;
        $this->_helper = $_helper;
        $this->_logger = $_logger;
        $this->_date = $_date;
    }

    public function execute()
    {
    	$params = $this->getRequest()->getParams();
		$isActive = $this->_helper->getActive();

		if($isActive) {
			$notificationModel = $this->_notificationFactory->create();

			$notificationModel->setData([
				'response_data' => json_encode($params),
				'status' => "",
				'created_at' => $this->_date->gmtDate('Y-m-d H:i:s')
			])->save();

            //falta actualizar el pedido
		}

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->_jsonResultFactory->create();
        $result->setHttpResponseCode(\Magento\Framework\Webapi\Response::HTTP_OK);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
