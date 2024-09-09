<?php

namespace Izipay\Core\Model;

class Izipay extends \Magento\Payment\Model\Method\AbstractMethod implements \Magento\Framework\DataObject\IdentityInterface
{
    protected $_code = 'izipay';

    const CACHE_TAG = 'izipay_core_izipay';
	protected $_cacheTag = 'izipay_core_izipay';
	protected $_eventPrefix = 'izipay_core_izipay';

	protected function _construct()
	{
		$this->_init('Izipay\Core\Model\ResourceModel\Izipay');
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
