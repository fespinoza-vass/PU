<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace WolfSellers\Flow\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\Flow\Helper\Cart as HelperCart;

class Updateitem implements HttpPostActionInterface
{

    /**
     * @var FormKeyValidator
     */
    private FormKeyValidator $formKeyValidator;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var HelperCart
     */
    protected HelperCart $helperCart;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;

    /**
     * UpdateItemQty constructor
     *
     * @param FormKeyValidator $formKeyValidator
     * @param Json $json
     * @param HelperCart $helperCart
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        FormKeyValidator $formKeyValidator,
        Json $json,
        HelperCart $helperCart,
        RequestInterface $request,
        ResponseInterface $response,
        ManagerInterface $messageManager
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->json = $json;
        $this->helperCart = $helperCart;
        $this->request = $request;
        $this->response = $response;
        $this->messageManager = $messageManager;
    }

    /**
     * Controller execute method
     *
     * @return void
     */
    public function execute(): void
    {
        try {
            $this->validateRequest();
            $this->validateFormKey();

            $itemId = (int) $this->request->getParam('itemId');
            $qty = (int) $this->request->getParam('itemQty');

            list($itemFound,$item) = $this->helperCart->getItem($itemId, 'getItemId');

            $product = $item->getProduct();
            $qty = isset($qty) ? (double) $qty : 0;

            if (!$itemFound) {
                throw new LocalizedException(
                    __('Something went wrong while saving the page. Please refresh the page and try again.')
                );
            }

            $a = $this->helperCart->updateItemQty($item, $qty);

            if ($a !== true) {
                $msg = !empty($a) ?
                    $a :
                    'Something went wrong while saving the page. Please refresh the page and try again.';

                throw new LocalizedException(__($msg));
            }

            $this->messageManager->addComplexSuccessMessage(
                'addCartSuccessMessage',
                $this->helperCart->getMessageInformation($product, $item, $qty)
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
    private function jsonResponse(string $error = ''): void
    {
        $this->response->representJson(
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
    private function validateRequest(): void
    {
        if ($this->request->isPost() === false) {
            throw new NotFoundException(__('Page Not Found'));
        }
    }

    /**
     * Validates form key
     *
     * @return void
     * @throws LocalizedException
     */
    private function validateFormKey(): void
    {
        if (!$this->formKeyValidator->validate($this->request)) {
            throw new LocalizedException(
                __('Something went wrong while saving the page. Please refresh the page and try again.')
            );
        }
    }
}
