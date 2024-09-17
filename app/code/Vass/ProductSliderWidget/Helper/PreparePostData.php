<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_ProductSliderWidget
 * @author VASS Team
 */

namespace Vass\ProductSliderWidget\Helper;

use Magento\Catalog\ViewModel\Product\Listing\PreparePostData as ViewModel;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class PreparePostData extends AbstractHelper
{
    /**
     * @var ViewModel
     */
    private ViewModel $viewModel;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ViewModel $viewModel
     */
    public function __construct(
        Context $context,
        ViewModel $viewModel
    ) {
        parent::__construct($context);
        $this->viewModel = $viewModel;
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     */
    public function getData(string $url, array $data = []): array
    {
        return $this->viewModel->getPostData($url, $data);
    }
}
