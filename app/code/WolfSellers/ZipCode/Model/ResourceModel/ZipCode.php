<?php


namespace WolfSellers\ZipCode\Model\ResourceModel;


Class ZipCode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wolfsellers_zipcode', 'zip_id');
    }

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }
}
