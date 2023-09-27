<?php


namespace WolfSellers\Bopis\Block\Adminhtml\Order\View;


use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Tax\Helper\Data as TaxHelper;
use WolfSellers\Bopis\Helper\Config;
use Magento\Framework\App\ObjectManager;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use WolfSellers\Bopis\Helper\RememberMeHelper;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Footer extends AbstractOrder
{
    private Json $serializer;
    private AuthSession $authSession;

    /** @var mixed|RememberMeHelper  */
    private mixed $rememberMeHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param array $data
     * @param ShippingHelper|null $shippingHelper
     * @param TaxHelper|null $taxHelper
     * @param Json|null $serializer
     * @param AuthSession|null $authSession
     * @param RememberMeHelper|null $rememberMeHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        array $data = [],
        ?ShippingHelper $shippingHelper = null,
        ?TaxHelper $taxHelper = null,
        ?Json $serializer = null,
        AuthSession $authSession = null,
        RememberMeHelper $rememberMeHelper = null
    ) {
        parent::__construct($context, $registry, $adminHelper, $data, $shippingHelper, $taxHelper);
        $this->serializer = $serializer ?? ObjectManager::getInstance()->get(Json::class);
        $this->authSession = $authSession ?? ObjectManager::getInstance()->get(AuthSession::class);
        $this->rememberMeHelper = $rememberMeHelper ?? ObjectManager::getInstance()->get(RememberMeHelper::class);
    }

    protected $deliverStatus = [
        "preparado"
    ];

    protected $deliveredStatus = [
        "complete"
    ];

    protected $prepareStatus = [
        "processing",
        "preparando"
    ];

    protected $cancelStatus = [
        "payment",
        "processing",
        "shipping"
    ];

    public function isEntregarAvailable(): bool
    {
        if(
            in_array($this->getOrder()->getStatus(),[$this->_scopeConfig->getValue('bopis/status/shipping')])
            || in_array($this->getOrder()->getStatus(),[$this->_scopeConfig->getValue('bopis/status/readyforpickup')])
          //  && $this->getOrder()->getData("verificacion_bopis_cliente_retira") > 0
          //  && $this->getOrder()->getData("verificacion_bopis_cliente_factura") > 0
          //  && $this->getOrder()->getData("verificacion_bopis_orden") > 0
          //  && $this->getOrder()->getData("verificacion_bopis_metodo_pago") > 0
        ) {
            return true;
        }
        return false;
    }

    public function isDelivered(): bool
    {
        if(
            (
                in_array($this->getOrder()->getState(),$this->deliveredStatus)
                && $this->getOrder()->getData("verificacion_bopis_cliente_retira") > 0
                && $this->getOrder()->getData("verificacion_bopis_cliente_factura") > 0
                && $this->getOrder()->getData("verificacion_bopis_orden") > 0
                && $this->getOrder()->getData("verificacion_bopis_metodo_pago") > 0
            ) || $this->getOrder()->getData("bopis_delivered") > 0
        ) {
            return true;
        }
        return false;
    }

    public function isHolded(): bool
    {
        if($this->getOrder()->getState() == "holded") {
            return true;
        }
        return false;
    }

    public function isPrepararAvailable(): bool
    {
        if($this->getOrder()->getStatus() == $this->_scopeConfig->getValue('bopis/status/confirmed')) {
            return true;
        }
        return false;
    }

    public function isEnviarAvailable(): bool
    {
        if($this->getOrder()->getStatus() == $this->_scopeConfig->getValue('bopis/status/preparing')) {
            return true;
        }
        return false;
    }

    public function isHoldAvailable(): bool
    {
        if(in_array($this->getOrder()->getStatus(),$this->cancelStatus) && $this->getOrder()->canHold()) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getHoldReasons(): array
    {
        $reasons = [];

        $reasonsConfig = $this->_scopeConfig->getValue(Config::XPATH_HOLD_REASONS);
        if($reasonsConfig == '' || $reasonsConfig == null) {
            return $reasons;
        }

        $data = $reasonsConfig;
        if(!is_array($data)) {
            $data = $this->serializer->unserialize($reasonsConfig);
        }

        foreach($data as $row)
        {
            $reasons[] = $row['reason'];
        }

        return $reasons;
    }

    public function isComplete(): bool
    {
        if($this->getOrder()->getStatus() == $this->_scopeConfig->getValue('bopis/status/complete')) {
            return true;
        }
        return false;
    }

    public function isEnviado(): bool
    {
        if($this->getOrder()->getStatus() == $this->_scopeConfig->getValue('bopis/status/shipping')) {
            return true;
        }
        return false;
    }

    public function isCompleteComments()
    {
        $comments = $this->getOrder()->getStatusHistoryCollection()->getData();
        $result = [];

        foreach($comments as $comment):
            if($comment['status'] =='complete'):
                $result[] = $comment['comment'];
            endif;
        endforeach;

        return $result;
    }

    public function getAdminUserType() {
        return $this->authSession->getUser()->getData('user_type');
    }

    /**
     * @return bool
     */
    public function popupEnabled()
    {
        if (!$role = $this->rememberMeHelper->getCurrentUserRole()) return false;

        if ($role == AbstractBopisCollection::BOPIS_SUPER_ADMIN ||
            $role == AbstractBopisCollection::BOPIS_STORES
        ) {
            return true;
        }

        return false;
    }
}
