<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace WolfSellers\Flow\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\Flow\Helper\Cart as HelperCart;

class Updateitem extends \Magento\Framework\App\Action\Action
                 implements \Magento\Framework\App\Action\HttpPostActionInterface
{

    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var HelperCart
     */
    protected $helperCart;

    /**
     * UpdateItemQty constructor
     *
     * @param Context $context
     * @param FormKeyValidator $formKeyValidator
     * @param Json $json
     * @param StockAvailable $stockAvailable
     * @param HelperCart $helperCart
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        Json $json,
        HelperCart $helperCart
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->json = $json;
        $this->helperCart = $helperCart;
        parent::__construct($context);
    }

    /**
     * Controller execute method
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->validateRequest();
            $this->validateFormKey();

            $itemId = (int)$this->getRequest()->getParam('itemId');
            $qty = (int)$this->getRequest()->getParam('itemQty');

            list($itemFound,$item) = $this->helperCart->getItem($itemId,"getItemId");
            $product = $item->getProduct();
            $qty = isset($qty) ? (double) $qty : 0;

            if (!$itemFound) {
                throw new LocalizedException(
                    __('Something went wrong while saving the page. Please refresh the page and try again.')
                );
            }

            $a = $this->helperCart->updateItemQty($item,$qty);
            if($a !== true){
                $msg = !empty($a) ? $a : 'Something went wrong while saving the page. Please refresh the page and try again.';
                throw new LocalizedException(__($msg));
            }
            $this->messageManager->addComplexSuccessMessage(
                'addCartSuccessMessage', $this->helperCart->getMessageInformation($product,$item,$qty)
            );
            $this->jsonResponse();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->jsonResponse('Something went wrong while saving the page. Please refresh the page and try again.');
        }
    }

    /**
     * JSON response builder.
     *
     * @param string $error
     * @return void
     */
    private function jsonResponse(string $error = '')
    {
        $this->getResponse()->representJson(
            $this->json->serialize($this->getResponseData($error))
        );
    }

    /**
     * Returns response data.
     *
     * @param string $error
     * @return array
     */
    private function getResponseData(string $error = ''): array
    {
        $response = ['success' => true];

        if (!empty($error)) {
            $response = [
                'success' => false,
                'error_message' => $error,
            ];
        }

        return $response;
    }

    /**
     * Validates the Request HTTP method
     *
     * @return void
     * @throws NotFoundException
     */
    private function validateRequest()
    {
        if ($this->getRequest()->isPost() === false) {
            throw new NotFoundException(__('Page Not Found'));
        }
    }

    /**
     * Validates form key
     *
     * @return void
     * @throws LocalizedException
     */
    private function validateFormKey()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(
                __('Something went wrong while saving the page. Please refresh the page and try again.')
            );
        }
    }
}
