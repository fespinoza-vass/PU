<?php

namespace PechoSolutions\Visanet\Controller\Visa;
  
class Router implements \Magento\Framework\App\RouterInterface
{
 
    protected $actionFactory;
  
    protected $_response;
  
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }
 
 
    public function match(\Magento\Framework\App\RequestInterface $request)
    {   
         // /vex/pay/{quote_hash} y /vex/pay/me/{token_session}

         $url = trim($request->getPathInfo(), '/');
 
         if(strpos($url, 'vex/pay/') !== false) {
  
             $urlParts = explode('/', $url); 
             $idHash = $urlParts[(count($urlParts)-1)];
 
              // Usuario no logeado 
              $request->setModuleName('visanet')
              ->setControllerName('visa')
              ->setActionName('testjose');
  
 
         } else { 
             return;
         }
  
  
         return $this->actionFactory->create(
             'Magento\Framework\App\Action\Forward',
             ['request' => $request]
         );
      
    }

}