<?php

namespace Vass\NiubizVisanet\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Vass\NiubizVisanet\Model\Config;

class Script extends \Magento\Framework\View\Element\Template
{
    /** @var Config  */
    private Config $config;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config =  $config;
    }

    /**
     * Get config info
     *
     * @return conf
     */
    public function getEnv()
    {
        $conf = $this->config->configurationNiubiz();

        return $conf['debug'];
    }
}
