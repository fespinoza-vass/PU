<?php

namespace Izipay\Core\Model;

class Notification extends \Magento\Payment\Model\Method\AbstractMethod implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'izipay_core_notification';
	protected $_cacheTag = 'izipay_core_notification';
	protected $_eventPrefix = 'izipay_core_notification';

	protected function _construct()
	{
		$this->_init('Izipay\Core\Model\ResourceModel\Notification');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}
