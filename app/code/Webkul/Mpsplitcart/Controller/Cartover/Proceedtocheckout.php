<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitcart
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsplitcart\Controller\Cartover;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

/**
 *  Webkul Mpsplitcart Cartover Proceedtocheckout controller
 */
class Proceedtocheckout extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var boolean
     */
    protected $_error = false;

    /**
     * @param Context                         $context
     * @param Session                         $customerSession
     * @param FormKeyValidator                $formKeyValidator
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Webkul\Mpsplitcart\Helper\Data $helper
    ) {
        $this->_session = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->helper     = $helper;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        try {
            $loginUrl = $this->_objectManager->get(
                \Magento\Customer\Model\Url::class
            )->getLoginUrl();

            return parent::dispatch($request);
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Controller_Proceedtocheckout dispatch : ".$e->getMessage());
        }
    }

    /**
     * Retrieve customer session object.
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        try {
            return $this->_session;
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Controller_Proceedtocheckout _getSession : ".$e->getMessage());
        }
    }

    /**
     * To proceed to checkout a selected cart
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                if (!$this->_formKeyValidator->validate($this->getRequest())) {
                    $this->messageManager->addError(__("Something Went Wrong !!!"));
                    return $this->resultRedirectFactory->create()->setPath(
                        'checkout/cart',
                        [
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    );
                }
                $fields = $this->getRequest()->getParams();
                $sellerId = $this->helper->getMpCustomerId();

                if (isset($fields['mpslitcart-checkout'])
                    && $fields['mpslitcart-checkout']!==""
                ) {
                    $this->helper->createCustomQuote();
                    $this->helper->getUpdatedQuote(
                        $fields['mpslitcart-checkout']
                    );
                    return $this->resultRedirectFactory->create()->setPath(
                        'checkout',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                } else {
                    if (isset($fields['mpslitcart-disbale'])
                        && $fields['mpslitcart-disbale']!==""
                    ) {
                        return $this->resultRedirectFactory->create()->setPath(
                            'checkout',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    } else {
                        foreach ($errors as $message) {
                            $this->messageManager->addError($message);
                        }

                        return $this->resultRedirectFactory->create()->setPath(
                            'checkout/cart',
                            [
                                '_secure' => $this->getRequest()->isSecure()
                            ]
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->helper->logDataInLogger("Controller_Proceedtocheckout execute : ".$e->getMessage());
                $this->messageManager->addError($e->getMessage());
                return $this->resultRedirectFactory->create()->setPath(
                    'checkout/cart',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            $sellerId = $this->getRequest()->getParam('sid');
            $this->helper->createCustomQuote();
            $this->helper->getUpdatedQuote($sellerId);
            return $this->resultRedirectFactory->create()->setPath(
                'multishipping/checkout/addresses',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
