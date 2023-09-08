<?php

namespace WolfSellers\Bopis\Plugin\Block\Adminhtml\User\Edit\Tab;

use Closure;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\User\Block\User\Edit\Tab\Main;

class UserFieldSource
{
    private SourceRepositoryInterface $sourceRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private WebsiteFactory $websiteFactory;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     */
    public function __construct (
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        WebsiteFactory $websiteFactory
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->websiteFactory = $websiteFactory;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function aroundGetFormHtml(
        Main $subject,
        Closure $proceed
    )
    {
        $form = $subject->getForm();
        if (is_object($form)) {

            #die(var_export($form->getHtml()));


            $sourceList = $this->getSourcesList();
            $options = array();
            $options["all"] = "";
            foreach ($sourceList as $source) {
                $options[$source->getData("source_code")] = $source->getData("frontend_name");
            }

            $fieldset = $form->getElement('base_fieldset');

            $fieldset->addField(
                'user_type',
                'select',
                [
                    'name' => 'user_type',
                    'label' => __('Tipo de Usuario'),
                    'id' => 'user_type',
                    'title' => __('user_type_title'),
                    'required' => false,
                    'options' => [
                        '0' => 'Usuario eCommerce',
                        '1' => 'Usuario Sucursal',
                        '2' => 'Usuario Supervisor Sucursales'
                    ]
                ]
            );

            $fieldset->addField(
                'source_code',
                'select',
                [
                    'name' => 'source_code',
                    'label' => __('Sucursal'),
                    'id' => 'source_code',
                    'title' => __('source_code_title'),
                    'required' => false,
                    'options' => $options
                ]
            );
            $websites = [ 0 => "" ];
            foreach ($this->websiteFactory->create()->getCollection() as $website) {
                $websites[$website->getWebsiteId()] = $website->getName();
            }

            //die(print_r($websites, true));
            $fieldset->addField(
                'website_id',
                'select',
                [
                    'name' => 'website_id',
                    'label' => __('Website'),
                    'id' => 'website_id',
                    'title' => __('website'),
                    'required' => false,
                    'options' => $websites
                ]
            );

            $subject->setForm($form);
        }
        return $proceed();
    }

    public function getSourcesList()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('enabled', 1)
            ->addFilter("source_code", array('default','online_CO','online_CR','online_PE'),'nin')
            ->create();
        $sourceData = $this->sourceRepository->getList($searchCriteria);
        return $sourceData->getItems();
    }
}
