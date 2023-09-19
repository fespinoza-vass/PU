<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Model\Email;

use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;


class SimpleSender
{
    /** @var string  */
    protected string $templateId;

    /** @var string  */
    protected string $sender;

    /**
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param SenderResolverInterface $senderResolver
     * @param Escaper $_escaper
     * @param StoreManagerInterface $_storeManager
     * @param Renderer $addressRenderer
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected StateInterface $inlineTranslation,
        protected TransportBuilder $transportBuilder,
        protected SenderResolverInterface $senderResolver,
        protected Escaper $_escaper,
        protected StoreManagerInterface $_storeManager,
        protected Renderer $addressRenderer,
        protected LoggerInterface $logger
    ){
    }

    /**
     * @param array $to
     * @param array $vars
     * @return void
     */
    public function send(array $to, array $vars = []): void
    {
        try {
            $this->inlineTranslation->suspend();
            $this->transportBuilder
            ->setTemplateIdentifier($this->getTemplateIdentifier())
            ->setTemplateVars($vars)
            ->setFromByScope($this->getSender())
            ->addTo($to['email'], $to['name'])
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            );
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Throwable $e){
            $this->logger->error($e->getMessage(), [$e->getTraceAsString()]);
        }
    }

    /**
     * @param $sender
     * @return void
     */
    public function setSender($sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function getSender(): array
    {
        $sender = $this->senderResolver->resolve($this->sender);

        if (!$sender) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please define the correct sender information.')
            );
        }

        return [
            'name' => $this->_escaper->escapeHtml($sender['name']),
            'email' => $this->_escaper->escapeHtml($sender['email']),
        ];
    }

    /**
     * @param $templateId
     * @return void
     */
    public function setTemplateIdentifier($templateId): void
    {
        $this->templateId = $templateId;
    }

    /**
     * @return string
     */
    protected function getTemplateIdentifier(): string
    {
        return $this->templateId;
    }

    /**
     * Render shipping address into html.
     *
     * @param Order $order
     * @return string|null
     */
    public function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Render billing address into html.
     *
     * @param Order $order
     * @return string|null
     */
    public function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

}
