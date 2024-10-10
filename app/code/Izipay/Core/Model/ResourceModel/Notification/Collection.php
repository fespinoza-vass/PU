<?php

namespace Izipay\Core\Model\ResourceModel\Notification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'izipay_core_notification_collection';
	protected $_eventObject = 'notification_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Izipay\Core\Model\Notification', 'Izipay\Core\Model\ResourceModel\Notification');
	}
}
