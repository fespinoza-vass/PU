<?php


namespace WolfSellers\Bopis\Block\Adminhtml\Order\View;


use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Helper\Admin;
use Magento\Sales\Model\Order\Address\Renderer;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class Info extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{

    private SourceRepositoryInterface $sourceRepository;
    private AuthSession $authSession;
    private ?SourceInterface $source = null;
    private BopisRepositoryInterface $bopisRepository;

    /**
     * @param AuthSession $authSession
     * @param SourceRepositoryInterface $sourceRepository
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param GroupRepositoryInterface $groupRepository
     * @param CustomerMetadataInterface $metadata
     * @param ElementFactory $elementFactory
     * @param Renderer $addressRenderer
     * @param BopisRepositoryInterface $bopisRepository
     * @param array $data
     */
    public function __construct(
        AuthSession $authSession,
        SourceRepositoryInterface $sourceRepository,
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        GroupRepositoryInterface $groupRepository,
        CustomerMetadataInterface $metadata,
        ElementFactory $elementFactory,
        Renderer $addressRenderer,
        \WolfSellers\Bopis\Api\BopisRepositoryInterface $bopisRepository,
        array $data = []
    ){
        parent::__construct(
            $context,
            $registry,
            $adminHelper,
            $groupRepository,
            $metadata,
            $elementFactory,
            $addressRenderer,
            $data
        );
        $this->sourceRepository = $sourceRepository;
        $this->authSession = $authSession;
        $this->bopisRepository = $bopisRepository;
    }

    public function getSourceName() {
        $name = "";
        if($this->getSource() != null) {
            $name = $this->getSource()->getName();
        }
        return $name;
    }

    protected function getSource()
    {
        if($this->source == null) {
            try {
                if (!empty($this->getBopis())){
                    $this->source = $this->sourceRepository->get($this->getBopis());
                }else{
                    $sourceCode = $this->authSession->getUser()->getData('source_code');
                    $this->source = $this->sourceRepository->get($sourceCode);
                }
            } catch (NoSuchEntityException $e) {
            }
        }
        return $this->source;
    }

    public function isBtnClienteRetiraAvailable() {
        return $this->getOrder()->getData("verificacion_bopis_cliente_retira") == 0
            && $this->getOrder()->getStatus() == "preparado";
    }

    public function isBtnClienteFacturaAvailable() {
        return $this->getOrder()->getData("verificacion_bopis_cliente_retira") > 0
            && $this->getOrder()->getData("verificacion_bopis_cliente_factura") == 0
            && $this->getOrder()->getStatus() == "preparado";
    }

    public function isBtnOrdenAvailable() {
        return $this->getOrder()->getData("verificacion_bopis_cliente_retira") > 0
            && $this->getOrder()->getData("verificacion_bopis_cliente_factura") > 0
            && $this->getOrder()->getData("verificacion_bopis_orden") == 0
            && $this->getOrder()->getStatus() == "preparado";
    }

    public function getBopis(){
        try {
            $bopis = $this->bopisRepository->getByQuoteId($this->getOrder()->getQuoteId());
            return $bopis->getStore();
        } catch (LocalizedException $e) {
            return null;
        }
    }

    public function getAdminUserType() {
        return $this->authSession->getUser()->getData('user_type');
    }

    public function isLegalPerson(){
        if ($this->getOrder()->getData('movimiento') == "Juridica"){
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function IsPickup()
    {
        if ($this->getOrder()->getShippingMethod() == \WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection::PICKUP_SHIPPING_METHOD) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getShippingStoreName()
    {
        if ($this->IsPickup()) {
            $title = explode('-', $this->getOrder()->getShippingDescription());
            return trim(end($title));
        }

        return $this->getSourceName();
    }
}
