<?php

namespace PechoSolutions\Visanet\Observer;

use Magento\Framework\Locale\Currency;
use Magento\Framework\Event\ObserverInterface;

class ModifyCurrencyPeruvianSol implements ObserverInterface
{

    protected $symbolFactory; // Según la documentación se incluye pero al parecer como solo se venderan productos con una moneda No seria necesario.

    public function __construct(\Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolFactory)
    {
        $this->symbolFactory = $symbolFactory;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $baseCode = $observer->getEvent()->getBaseCode();
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $originalOptions = $currencyOptions->getData();

        $currencyOptions->setData($this->getCurrencyOptions($baseCode, $originalOptions));


        return $this;
    }


    protected function getCurrencyOptions($baseCode, $originalOptions)
    {
        $currencyOptions = [];

        if ($baseCode == 'PEN') {

            $currencyOptions['position'] = \Magento\Framework\Currency::LEFT;
            $currencyOptions[Currency::CURRENCY_OPTION_SYMBOL] = 'S/';
            $currencyOptions[Currency::CURRENCY_OPTION_DISPLAY] = \Magento\Framework\Currency::USE_SYMBOL;

        }

        return array_merge($originalOptions, $currencyOptions);
    }
}