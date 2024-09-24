<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;

class OrderStatus extends Field
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Context $context,
        Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        if (!$this->moduleManager->isEnabled('Amasty_RewardsProFunctionality')) {
            $element->setData('disabled', 'disabled');
            $element->setData(
                'comment',
                'Awarding reward points on the \'Complete\' status is the default behavior.' .
                ' The possibility of utilizing all other statuses is provided as part of an active ' .
                'product subscription or support subscription. ' .
                'To upgrade and access this functionality, please follow the ' .
                '<a href="https://amasty.com/amcustomer/account/products/' .
                '?utm_source=extension&utm_medium=backend&utm_campaign=upgrade_rewardpoints">link</a>.'
            );
        }

        return parent::_getElementHtml($element);
    }
}
