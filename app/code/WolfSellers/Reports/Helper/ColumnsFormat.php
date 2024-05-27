<?php
namespace WolfSellers\Reports\Helper;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class ColumnsFormat extends AbstractHelper
{
    /**
     * @var Visibility
     */
    private Visibility $catalogProductVisibility;

    /**
     * @var Status
     */
    private Status $catalogProductStatus;

    /**
     * @param Context $context
     * @param Visibility $catalogProductVisibility
     * @param Status $catalogProductStatus
     */
    public function __construct(
        Context $context,
        Visibility $catalogProductVisibility,
        Status $catalogProductStatus
    )
    {
        parent::__construct($context);
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->catalogProductStatus = $catalogProductStatus;
    }

    /**
     * @param string $column
     * @param $value
     * @return mixed|string|null
     */
    public function getColumnFormat(string $column, $value): mixed
    {
        switch ($column){
            case "salable_quantity":
            case "quantity_per_source":
                return array_pop($value)['qty'];
            case "visibility":
                return $this->catalogProductVisibility->getOptionText($value);
            case "status":
                return $this->catalogProductStatus->getOptionText($value);
            default:
                return (is_array($value)) ? implode("\n", $value) : $value;
        }
    }
}
