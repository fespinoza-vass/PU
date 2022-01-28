<?php

namespace adobe\adobeSignCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class ConfigChange implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    private $configWriter;

    /**
     * ConfigChange constructor.
     * @param RequestInterface $request
     * @param WriterInterface $configWriter
     */
    public function __construct(
        RequestInterface $request,
        WriterInterface $configWriter
    ) {
        $this->request = $request;
        $this->configWriter = $configWriter;

    }

    public function execute(EventObserver $observer)
    {
        $fields = $this->request->getParam('groups')['general']['fields'];
        $security = $fields['authMethod']['value'] === 'PASSWORD' ? array("authenticationMethod" => "PASSWORD", "password" => $fields['password']['value']) :
            array("authenticationMethod" => $fields['authMethod']['value']);
        $payload = array(
            "emailOption" => array(
                "sendOptions" => array(
                    "completionEmails" => "ALL",
                    "inFlightEmails" => "NONE",
                    "initEmails" => "ALL"
                )
            ),
            "name" => "Magento Purchase Agreement",
            "participantSetsInfo" => [array(
                "memberInfos" => [array(
                    "securityOption" => $security
                )],
                "order" => 1,
                "role" => $fields['role']['value']
            )],
            "signatureType" => "ESIGN",
            "state" => "IN_PROCESS"
        );
        $this->configWriter->save('adobesign2/general/json', json_encode($payload));
        return $this;
    }
}
