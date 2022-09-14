<?php
namespace WolfSellers\Reports\Helper;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class ColumnsFormat extends AbstractHelper{
    /**
     * @var Visibility
     */
    private $catalogProductVisibility;
    /**
     * @var Status
     */
    private $catalogProductStatus;

    public function __construct(
        Context $context,
        Visibility $catalogProductVisibility,
        Status $catalogProductStatus
    ){
        parent::__construct($context);
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->catalogProductStatus = $catalogProductStatus;
    }

    public function getColumnFormat(string $column, $value){
        switch ($column){
            case "salable_quantity":
            case "quantity_per_source":
                return array_pop($value)['qty'];
            case "visibility":
                return $this->catalogProductVisibility->getOptionText($value);
            case "status":
                return $this->catalogProductStatus->getOptionText($value);
            default:
                if (is_array($value))
                    return print_r($value,true);
                else
                    return $value;
        }
    }

}
