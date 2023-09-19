<?php

namespace WolfSellers\Bopis\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\Customform\Helper\Data;
use WolfSellers\Bopis\Model\Email\Identity\SatisfactionSurvey;


class AmastyForm implements ArgumentInterface
{

    /**
     * @param Data $customFormHelper
     * @param SatisfactionSurvey $satisfactionSurvey
     */
    public function __construct(
        protected Data                  $customFormHelper,
        protected SatisfactionSurvey    $satisfactionSurvey,
        protected StoreManagerInterface $storeManager
    )
    {
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getSatisfactionSurveyId(): int
    {
        return $this->satisfactionSurvey->getAmastyFormId();

    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function renderSatisfactionSurvey()
    {
        $form = $this->customFormHelper->renderForm(
            $this->getSatisfactionSurveyId()
        );

        return (!empty($form)) ?
            $form :
            "Encuesta no disponible <a href='" . $this->storeManager->getStore()->getBaseUrl() . "'>Ir a inicio</a>";
    }
}
