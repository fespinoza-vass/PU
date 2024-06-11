<?php
namespace Vass\General\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Checkout\Model\Cart;

class CartHelper extends AbstractHelper
{
	protected $_cart;

	public function __construct(Cart $cart){
		$this->_cart = $cart;
	}

	public function removeShippingData(){
		try {
			$address = $this->_cart->getQuote()->getShippingAddress();
			$rates = $address->collectShippingRates();
			$rates->delete();
			
			return true;
		}catch (Exception $e) {
			return false;
		}
	}
}
?>