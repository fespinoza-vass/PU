<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model;

use Magento\Framework\Api\DataObjectHelper;
use WolfSellers\Consecutive\Api\Data\SequentialInterface;
use WolfSellers\Consecutive\Api\Data\SequentialInterfaceFactory;

class Sequential extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'wolfsellers_consecutive_sequential';
    protected $dataObjectHelper;

    protected $sequentialDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param SequentialInterfaceFactory $sequentialDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \WolfSellers\Consecutive\Model\ResourceModel\Sequential $resource
     * @param \WolfSellers\Consecutive\Model\ResourceModel\Sequential\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        SequentialInterfaceFactory $sequentialDataFactory,
        DataObjectHelper $dataObjectHelper,
        \WolfSellers\Consecutive\Model\ResourceModel\Sequential $resource,
        \WolfSellers\Consecutive\Model\ResourceModel\Sequential\Collection $resourceCollection,
        array $data = []
    ) {
        $this->sequentialDataFactory = $sequentialDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve sequential model with sequential data
     * @return SequentialInterface
     */
    public function getDataModel()
    {
        $sequentialData = $this->getData();
        
        $sequentialDataObject = $this->sequentialDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $sequentialDataObject,
            $sequentialData,
            SequentialInterface::class
        );
        
        return $sequentialDataObject;
    }
}

