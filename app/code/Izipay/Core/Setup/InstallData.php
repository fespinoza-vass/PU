<?php

namespace Izipay\Core\Setup;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

class InstallData implements InstallDataInterface
{
    const CUSTOM_STATUS_CODE = 'payment_confirmed_izipay';
    const CUSTOM_STATE_CODE = 'payment_confirmed_izipay';
    const CUSTOM_STATUS_LABEL = 'Pago confirmado por Izipay';

    protected $statusFactory;
    protected $statusResourceFactory;

    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addCustomOrderStatus();
    }

    protected function addCustomOrderStatus()
    {
        $statusResource = $this->statusResourceFactory->create();
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => self::CUSTOM_STATUS_CODE,
            'label' => self::CUSTOM_STATUS_LABEL,
        ]);
        try {
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {
            return;
        }

        $status->assignState(self::CUSTOM_STATE_CODE, true, true);
    }
}