<?php
namespace Vass\Test\Controller\Cart;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart;

class Info extends \Magento\Framework\App\Action\Action
{

    protected $resultJsonFactory;
    protected $_cart;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Cart $cart
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_cart = $cart;

        parent::__construct($context);
    }

    public function execute()
    {
        try{
            $resultJson = $this->resultJsonFactory->create();

            $idCart = $this->_cart->getQuote()->getId();
            echo 'ID Cart: '.$idCart;
            echo "<br /><br />";

            $items = $this->_cart->getQuote()->getAllItems();

            foreach($items as $item) {
                echo 'ID: '.$item->getProductId().'<br>';
                echo 'Name: '.$item->getName().'<br>';
                echo 'Sku: '.$item->getSku().'<br>';
                echo 'Quantity: '.$item->getQty().'<br>';
                echo 'Price: '.$item->getPrice().'<br>';
                echo "<br />";            
            }

            $address = $this->_cart->getQuote()->getShippingAddress();

            echo 'ID Cart Address: '.$address->getId();
            echo "<br /><br />";

            if($address->collectShippingRates()){
                $rates = $address->collectShippingRates();
                echo "Monto Despacho: ".$rates->getData("shipping_amount")."<br>";           
                //$rates->setData("shipping_amount",0);
                $rates->delete();
            }

            /*$data = [];
            $resultJson->setData($data);

            return $resultJson;*/      
        }catch (\Exception $e){
            echo "ERROR";
        }
    }
}