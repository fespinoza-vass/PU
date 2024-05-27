<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model;

use Magento\Framework\Api\DataObjectHelper;
use WolfSellers\Consecutive\Api\Data\ConsecutiveInterface;
use WolfSellers\Consecutive\Api\Data\ConsecutiveInterfaceFactory;

class Consecutive extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $consecutiveDataFactory;

    protected $_eventPrefix = 'wolfsellers_consecutive_consecutive';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConsecutiveInterfaceFactory $consecutiveDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \WolfSellers\Consecutive\Model\ResourceModel\Consecutive $resource
     * @param \WolfSellers\Consecutive\Model\ResourceModel\Consecutive\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ConsecutiveInterfaceFactory $consecutiveDataFactory,
        DataObjectHelper $dataObjectHelper,
        \WolfSellers\Consecutive\Model\ResourceModel\Consecutive $resource,
        \WolfSellers\Consecutive\Model\ResourceModel\Consecutive\Collection $resourceCollection,
        array $data = []
    ) {
        $this->consecutiveDataFactory = $consecutiveDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve consecutive model with consecutive data
     * @return ConsecutiveInterface
     */
    public function getDataModel()
    {
        $consecutiveData = $this->getData();
        
        $consecutiveDataObject = $this->consecutiveDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $consecutiveDataObject,
            $consecutiveData,
            ConsecutiveInterface::class
        );
        
        return $consecutiveDataObject;
    }
}

