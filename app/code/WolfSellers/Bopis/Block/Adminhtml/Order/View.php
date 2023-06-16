<?php


namespace WolfSellers\Bopis\Block\Adminhtml\Order;


use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Registry;
use Magento\Sales\Helper\Reorder;
use Magento\Sales\Model\ConfigInterface;

class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    private RedirectInterface $redirect;

    /**
     * @param RedirectInterface $redirect
     * @param Context $context
     * @param Registry $registry
     * @param ConfigInterface $salesConfig
     * @param Reorder $reorderHelper
     * @param array $data
     */
    public function __construct(
        RedirectInterface $redirect,
        Context $context,
        Registry $registry,
        ConfigInterface $salesConfig,
        Reorder $reorderHelper,
        array $data = []
    ) {
        $this->redirect = $redirect;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    protected function _construct()
    {
        $this->addButton(
            'bopis_print',
            [
                'label'   => __('Print'),
                'class'   => 'bopis_print',
                'onclick' => 'setLocation(\'' . $this->getPdfPrintUrl() . '\')'
            ]
        );

        parent::_construct();
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * @return string
     */
    public function getPdfPrintUrl()
    {
        return $this->getUrl('bopis/order/printorder/order_id/' . $this->getOrderId());
    }

}
