<?php

namespace WolfSellers\Bopis\Controller\Modal;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Block\DataProviders\PostCodesPatternsAttributeData;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Session;
use Magento\CustomerCustomAttributes\ViewModel\FileAttribute;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\Form\AttributeMapper;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class Addresses implements HttpGetActionInterface {
    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * @var Session
     */
    private Session $customerSession;
    private RequestInterface $request;
    private BopisRepositoryInterface $bopisRepository;
    private CheckoutSession $checkoutSession;
    private PostCodesPatternsAttributeData $postcodeProvider;
    private AttributeMetadataDataProvider $attributeMetadataDataProvider;
    private AttributeMapper $attributeMapper;
    private FileAttribute $fileAttribute;

    /**
     * @param JsonFactory $jsonFactory
     * @param PageFactory $pageFactory
     * @param Session $customerSession
     */
    public function __construct(
        JsonFactory $jsonFactory,
        PageFactory $pageFactory,
        Session $customerSession,
        RequestInterface $request,
        BopisRepositoryInterface $bopisRepository,
        CheckoutSession $checkoutSession,
        PostCodesPatternsAttributeData $postcodeProvider
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->bopisRepository = $bopisRepository;
        $this->checkoutSession = $checkoutSession;
        $this->postcodeProvider = $postcodeProvider;
    }

    /**
     * @return Json
     */
    public function execute(): Json {
        $jsonResponse = $this->jsonFactory->create();

        $addresses = $this->_getAddresses();

        $resultPage = $this->pageFactory->create();
        $widgetMiddlename = $resultPage->getLayout()->createBlock("\Magento\Customer\Block\Widget\Name");
        $isActive = $widgetMiddlename->showMiddlename();
        $isRequired = $widgetMiddlename->isMiddlenameRequired();
        $middleName = $widgetMiddlename->getStoreLabel('middlename');
        $middleNameValidations = $widgetMiddlename->getAttributeValidationClass('middlename');

        $response['content']['form'] = $resultPage->getLayout()
            ->createBlock('WolfSellers\Bopis\Block\Address\Form')
            ->setTemplate('WolfSellers_Bopis::address/edit.phtml')
            ->setData('is_logged_in', $this->_isLoggedIn())
            ->setData('post_code_config', $this->postcodeProvider)
            ->setData('is_middlename_required', $isRequired)
            ->setData('show_middlename', $isActive)
            ->setData('middlename_store_label', $middleName)
            ->setData('middlename_attribute_validation_class', $middleNameValidations)
            ->toHtml();

        if (count($addresses) > 0) {
            $response['content']['grid'] = $resultPage->getLayout()
                ->createBlock('WolfSellers\Bopis\Block\Address\Grid')
                ->setTemplate('WolfSellers_Bopis::address/grid.phtml')
                ->setData('addresses', $addresses)
                ->toHtml();
        }

        if ($this->request->getParam("new_address")){
            $response['content']['grid'] = $resultPage->getLayout()
                ->createBlock('Magento\Customer\Block\Form\Register')
                ->setTemplate('WolfSellers_Bopis::address/new-address.phtml')
                ->setData('address', $this->_getAddress())
                ->toHtml();
            $jsonResponse->setStatusHeader(200);
            $jsonResponse->setData($response);

            return $jsonResponse;
        }

        $jsonResponse->setStatusHeader(200);
        $jsonResponse->setData($response);

        return $jsonResponse;
    }

    /**
     * @return array
     */
    private function _getAddresses(): array {
        if ($this->_isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();

            return $customer->getAddresses();
        } else {
            return [];
        }
    }

    /**
     * @return bool
     */
    private function _isLoggedIn(): bool {
        return $this->customerSession->isLoggedIn();
    }

    private function _getAddress(): array{
        try {
            $bopis = $this->bopisRepository->getByQuoteId($this->checkoutSession->getQuoteId());
            return json_decode($bopis->getAddressObject(), true);
        } catch (LocalizedException $e) {
            return [];
        }
    }
}
