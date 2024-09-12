<?php

namespace Izipay\Core\Model\ResourceModel\Izipay;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'izipay_core_izipay_collection';
	protected $_eventObject = 'izipay_collection'; // izipay_core

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Izipay\Core\Model\Izipay', 'Izipay\Core\Model\ResourceModel\Izipay');
	}
}
