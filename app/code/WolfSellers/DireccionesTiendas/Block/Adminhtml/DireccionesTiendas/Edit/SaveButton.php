<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Block\Adminhtml\DireccionesTiendas\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Direccionestiendas'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}

