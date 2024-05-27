<?php
namespace WolfSellers\Consecutive\Model;

use WolfSellers\Consecutive\Api\SequentialRepositoryInterface;
use WolfSellers\Consecutive\Model\ResourceModel\Consecutive\Collection as ConsecutiveCollection;
use WolfSellers\Consecutive\Model\ResourceModel\Sequential\Collection as SequentialCollection;
use WolfSellers\Consecutive\Model\Consecutive;
use \WolfSellers\Consecutive\Api\Data\ConsecutiveInterfaceFactory;
use WolfSellers\Consecutive\Api\ConsecutiveRepositoryInterface;

class ConsecutiveBuilder
{
    /** @var ConsecutiveCollection  */
    protected $_consecutiveCollection;

    /** @var SequentialRepositoryInterface  */
    protected $_sequentialRepository;

    /** @var SequentialCollection  */
    protected $_sequentialCollection;

    /** @var Consecutive  */
    protected $_consecutiveModel;

    /** @var ConsecutiveInterfaceFactory  */
    protected $_consecutiveFactory;

    /** @var ConsecutiveRepositoryInterface  */
    protected $_consecutiveRepository;

    protected $_consecutiveAvailable;

    public function __construct
    (
        SequentialRepositoryInterface $sequentialRepository,
        ConsecutiveCollection $consecutiveCollection,
        SequentialCollection $sequentialCollection,
        Consecutive $consecutiveModel,
        ConsecutiveInterfaceFactory $consecutiveFactory,
        ConsecutiveRepositoryInterface $consecutiveRepository
    )
    {
        $this->_consecutiveCollection = $consecutiveCollection;
        $this->_sequentialCollection = $sequentialCollection;
        $this->_sequentialRepository = $sequentialRepository;
        $this->_consecutiveModel = $consecutiveModel;
        $this->_consecutiveFactory = $consecutiveFactory;
        $this->_consecutiveRepository = $consecutiveRepository;
    }

    /**
     * @param $websiteId
     * @return false|mixed
     */
    public function getNextConsecutiveByStore($websiteId){

        $this->_sequentialCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);

        $sequentialCollection = $this->_sequentialCollection->addFieldToSelect(array('website_id','format','start_number'));
        $sequentialCollection->addFieldToFilter('website_id',['eq' => $websiteId]);
        $total = $sequentialCollection->getSize();

        if($total === 0){
            return false;
        }

        $format = $sequentialCollection->getFirstItem()->getFormat();

        $consecutiveData = $this->getLastConsecutive($websiteId);

        // Ya existen consecutivos en el website_id
        if($consecutiveData){
            $lastConsecutive = $consecutiveData->getData('consecutive_number');
            return $this->getSequentialNumberFromString($lastConsecutive,$format,true);
        }else{
            // Aun no existe ningun consecutivo toma el primero del sequential
            $folioStart = $sequentialCollection->getFirstItem()->getStartNumber();
            return $folioStart;
        }

    }

    /**
     * @param $websiteId
     * @return false|\Magento\Framework\DataObject
     */
    public function getLastConsecutive($websiteId){
        $this->_consecutiveCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
        $consecutiveCollection = $this->_consecutiveCollection->addFieldToSelect(array('consecutive_id','consecutive_number','website_id'));

        $consecutiveCollection->addFieldToFilter('website_id',['eq' => $websiteId]);
        $consecutiveCollection->setOrder('consecutive_id',"DESC");

        $total = $consecutiveCollection->getSize();

        if($total === 0){
            return false;
        }else{
            return $consecutiveCollection->getFirstItem();
        }
    }


    /**
     * @param $string
     * @param $formato
     * @param $letter
     * @return array|false
     */
    public function getElementByPattern($string,$formato,$letter){

        $posStart = strpos( $formato, $letter );

        if($posStart!==false) {
            $posEnd = (strlen($formato) - 1) - strpos(strrev($formato), strrev($letter));
            $patternLengh = (int)($posEnd - $posStart) + 1;
            $content = substr($string,$posStart,$patternLengh);
            return array('start'=>$posStart,'end'=>$posEnd,'length'=>$patternLengh,'content'=>$content);
        }else{
            return false;
        }
    }


    /**
     * @param $websiteId
     * @return array|false
     */
    public function getNewConsecutiveToAssign($websiteId){
        $consecutive = $this->_consecutiveFactory->create();
        $numeroIntentos = 0;
        do{
            try {
                $this->_consecutiveAvailable = "";
                $this->_consecutiveAvailable = $this->getNextConsecutiveByStore($websiteId);
                $consecutive->setWebsiteId($websiteId)
                            ->setConsecutiveNumber($this->_consecutiveAvailable);
                $consecutiveNew = $this->_consecutiveRepository->save($consecutive);
            }catch(\Exception $exception){
                $numeroIntentos++;
                continue;
            }
            break;
        }while($numeroIntentos<100);

        if($numeroIntentos>=100){
            return false;
        }

        return ['consecutive_id'=> $consecutiveNew->getConsecutiveId(), 'consecutive_name' => $consecutiveNew->getConsecutiveNumber()];
    }

    /**
     * @param $consecutiveString
     * @param $format
     * @param bool $next
     * @return mixed
     */
    public function getSequentialNumberFromString($consecutiveString,$format,$next = true){

        $alfa = $this->getElementByPattern($consecutiveString,$format,"A");
        $consecutivo = $this->getElementByPattern($consecutiveString,$format,"C");
        $renovacion = $this->getElementByPattern($consecutiveString,$format,"R");
        $sequentialNumber = (int) $consecutivo['content'];

        if($next){
            $sequentialNumber++;
        }
        $sequentialZeros = str_pad($sequentialNumber, $consecutivo['length'],"0",STR_PAD_LEFT);
        $consecutiveFinal = substr_replace($consecutiveString,$sequentialZeros,$consecutivo['start'],$consecutivo['length']);

        return $consecutiveFinal;

    }
}

