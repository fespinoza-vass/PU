<?php
namespace Izipay\Core\Controller\Quote;

class Save extends \Magento\Framework\App\Action\Action
{
	protected $quoteIdMaskFactory;
    protected $quoteRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if ($post) {
            $cartId       = $post['cartId'];
            $izipay_alternative_payment_method = isset($post['izipay_alternative_payment_method'])?$post['izipay_alternative_payment_method']:"all";
            $izipay_document_type = $post['izipay_document_type'];
            $izipay_document_number = $post['izipay_document_number'];
            $izipay_razon_social = $post['izipay_razon_social'];

            $izipay_transaction_id = $post['izipay_transaction_id'];
            $izipay_order_number = $post['izipay_order_number'];
            $izipay_payment_code_response = $post['izipay_payment_code_response'];

            $loggin       = $post['is_customer'];

            if ($loggin === 'false') {
                $cartId = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getQuoteId();
            }

            $quote = $this->quoteRepository->getActive($cartId);
            if (!$quote->getItemsCount()) {
                throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
            }

            $quote->setData('izipay_alternative_payment_method', $izipay_alternative_payment_method);
            $quote->setData('izipay_document_type', $izipay_document_type);
            $quote->setData('izipay_document_number', $izipay_document_number);
            $quote->setData('izipay_razon_social', $izipay_razon_social);

            $quote->setData('izipay_transaction_id', $izipay_transaction_id);
            $quote->setData('izipay_order_number', $izipay_order_number);
            $quote->setData('izipay_payment_code_response', $izipay_payment_code_response);
            
            $this->quoteRepository->save($quote);
        }
    }
}
