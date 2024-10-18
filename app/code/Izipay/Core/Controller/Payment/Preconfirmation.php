<?php
namespace Izipay\Core\Controller\Payment;

use \Izipay\Core\Helper\Data as IzipayHelper;
use Magento\Framework\Controller\ResultInterface;
use \Izipay\Core\Model\IzipayFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Sales\Model\Order;
use \Izipay\Core\Logger\Logger;

class Preconfirmation extends Action
{
	protected $_izipayFactory;
	protected $_logger;
	protected $_date;
	protected $_order;

	public function __construct(
		IzipayFactory $_izipayFactory,
		IzipayHelper $_helper,
		Logger $_logger,
		Context $context,
		DateTime $_date,
		Order $_order
	)
	{
		$this->_izipayFactory = $_izipayFactory;
		$this->_logger = $_logger;
		$this->_helper = $_helper;
		$this->_date = $_date;
		$this->_order = $_order;

		return parent::__construct($context);
	}

	public function execute()
	{
		$data = $this->getRequest()->getParams();
        $payment = $this->_helper->getPaymentData($data['payment_id']);
        $paymentArray = $payment->getAttributes();
        $paymentArray['payer'] = $payment->payer->getAttributes();
        $paymentArray['card']  = $payment->card->getAttributes();

		$izipayModel = $this->_izipayFactory->create()->load($data['preference_id'],'preference_id');
		$izipayModel = $izipayModel->addData([
			'preference_response' => json_encode($data),
			'payment_detail' => json_encode($paymentArray),
			'payment_status' => $payment->status,
			'updated_at' => $this->_date->gmtDate('Y-m-d H:i:s')
		])->save();

		$order = $this->_order->loadByIncrementId($izipayModel->getIncrementId());
		$orderCommentary  = $this->_helper->getPaymentStatuses($paymentArray['status_detail']).'<br>';
		$orderCommentary .= 'ORDER ID: '.$paymentArray['order']->id.'<br>';
		$orderCommentary .= 'ORDER TYPE: '.$paymentArray['order']->type.'<br>';
		$orderCommentary .= 'STATUS: '.$paymentArray['status'].'<br>';
		$orderCommentary .= 'STATUS DETAIL: '.$paymentArray['status_detail'].'<br>';
		$orderCommentary .= 'PAYMENT TYPE ID: '.$paymentArray['currency_id'].'<br>';
		$orderCommentary .= 'PAYMENT METHOD ID: '.$paymentArray['payment_type_id'].'<br>';
		$orderCommentary .= 'CURRENCY ID: '.$paymentArray['payment_method_id'].'<br>';
		$orderCommentary .= 'INSTALLMENT AMOUNT: '.$paymentArray['transaction_details']->installment_amount.'<br>';
		$orderCommentary .= 'TOTAL PAID AMOUNT: '.$paymentArray['transaction_details']->total_paid_amount.'<br>';
		$orderCommentary .= 'CARD: '.$paymentArray['card']['first_six_digits'].'******'.$paymentArray['card']['last_four_digits'].'<br>';
		$orderCommentary .= 'LIVE MODE: '.$paymentArray['live_mode'].'<br>';
		$orderCommentary .= 'BINARY MODE: '.$paymentArray['binary_mode'].'<br>';
		$orderCommentary .= 'DATE CREATED: '.$paymentArray['date_created'].'<br>';
		$orderCommentary .= 'DATE APPROVED: '.$paymentArray['date_approved'].'<br>';

		if($payment->status == 'approved'){
    		$order->setStatus('payment_confirmed_izipay');
    		$order->addStatusHistoryComment('Orden pagada con Izipay<br>'. $orderCommentary);
    		$order->save();

			//$this->_eventManager->dispatch('erp_order_save', ['increment_id' => $order->getId(),'payment_response'=> $paymentArray]);

		}else{
			$order->addStatusHistoryComment('Orden cancelada con Izipay<br>'.$orderCommentary);
			$order->cancel()->save();
		}

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirect->setPath('izipay/payment/confirmation',['id'=>base64_encode($izipayModel->getId())]);

        return $redirect;
	}
}
