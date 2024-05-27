<?php

namespace WolfSellers\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepositoryInterface;
use Magento\GiftMessage\Api\OrderRepositoryInterface as OrderRepositoryInterfaceGif;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Email extends AbstractHelper
{
    const XPATH_DEFAULT_EMAIL_FROM ='email/email_gift/email_from';
    /**
     * @var StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var Escaper
     */
    protected $escaper;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        OrderRepositoryInterface $orderRepository,
        OrderRepositoryInterfaceGif $orderRepositoryGif,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->orderRepository = $orderRepository;
        $this->orderGiftRepo = $orderRepositoryGif;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param $orderId
     * @return void
     */
    public function sendEmail($orderId)
    {
        try {
            $gifMessage = $this->getGiftMessages($orderId);
            $destinatario =$gifMessage["sender"];
            $recipient =$gifMessage["recipient"];
            $message =$gifMessage["message"];
            $emailFrom = $this->getEmailFrom();

            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml('Perfumerias Unidas'),
                'email' => $this->escaper->escapeHtml($emailFrom),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('email_gift_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'destinatario'  => $destinatario,
                    'recipient' => $recipient,
                    'message' => $message
                ])
                ->setFrom($sender)
                ->addTo($recipient)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * @param $order_id
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGiftMessages($order_id) {
        $order = $this->orderRepository->get($order_id);
        if($order->getGiftMessageId() != 0){
            $giftMessage = $this->orderGiftRepo->get($order_id);
            return $giftMessage;
        }
    }
    public function getEmailFrom(){
        return $this->_scopeConfig->getValue(self::XPATH_DEFAULT_EMAIL_FROM, ScopeInterface::SCOPE_STORE);
    }
}
