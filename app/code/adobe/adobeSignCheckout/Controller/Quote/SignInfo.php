<?php

namespace adobe\adobeSignCheckout\Controller\Quote;
use Magento\Framework\Controller\ResultFactory;

class SignInfo extends \Magento\Framework\App\Action\Action
{
    protected $quoteRepository;
    protected $request;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
            \Magento\Framework\App\Request\Http $request
    ) {
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context);
        $this->request = $request;
    }

    /**
     * @return json
     */
    public function execute()
    {
        $id = $this->request->getParam('id');
        $quote = $this->quoteRepository->get($id);

        $response['quoteId'] = $quote->getId();
        $response['signUrl'] = $quote->getData('sign_url');
        $response['signStatus'] = $quote->getData('sign_status');
        $response['agreementId'] = $quote->getData('agreement_id');

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
    }
}
