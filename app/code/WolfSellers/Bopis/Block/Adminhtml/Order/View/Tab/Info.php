<?php


namespace WolfSellers\Bopis\Block\Adminhtml\Order\View\Tab;


use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Helper\Admin;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Tax\Helper\Data as TaxHelper;

class Info extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\Info
{

    private PaymentHelper $paymentHelper;
    private Session $authSession;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param Session $authSession
     * @param array $data
     * @param ShippingHelper|null $shippingHelper
     * @param TaxHelper|null $taxHelper
     * @param PaymentHelper|null $paymentHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = [],
        ?ShippingHelper $shippingHelper = null,
        ?TaxHelper $taxHelper = null,
        ?PaymentHelper $paymentHelper = null
    )
    {
        parent::__construct($context, $registry, $adminHelper, $data, $shippingHelper, $taxHelper);
        $this->paymentHelper = $paymentHelper ?? ObjectManager::getInstance()->get(PaymentHelper::class);
        $this->authSession = $authSession;
    }

    /**
     * Get payment html
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPaymentHtml()
    {
        $paymentInfoBlock = $this->paymentHelper->getInfoBlock($this->getOrder()->getPayment(), $this->getLayout());

        $paymentInfoBlock->setTemplate("WolfSellers_Bopis::payment/info/default.phtml");
        return $paymentInfoBlock->toHtml();
    }

    public function isBtnMetodoPagoAvailable() {
        return $this->getOrder()->getData("verificacion_bopis_cliente_retira") > 0
            && $this->getOrder()->getData("verificacion_bopis_cliente_factura") > 0
            && $this->getOrder()->getData("verificacion_bopis_orden") > 0
            && $this->getOrder()->getData("verificacion_bopis_metodo_pago") == 0
            && $this->getOrder()->getStatus() == "preparado";
    }

    public function getAdminUserType() {
        return $this->authSession->getUser()->getData('user_type');
    }

    /**
     * @return void
     */
    public function getTiloData()
    {
        $history = $this->getOrder()->getStatusHistories();
        foreach ($history AS $item){
            echo '<div class="title">'.$item->getComment().'</div>';
        }
    }
}
