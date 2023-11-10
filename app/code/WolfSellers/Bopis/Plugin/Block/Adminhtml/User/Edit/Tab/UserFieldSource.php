<?php

namespace WolfSellers\Bopis\Plugin\Block\Adminhtml\User\Edit\Tab;

use Closure;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\User\Block\User\Edit\Tab\Main;

class UserFieldSource
{
    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var WebsiteFactory
     */
    private WebsiteFactory $websiteFactory;

    /**
     * @var Registry
     */
    protected Registry $_coreRegistry;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @param WebsiteFactory $websiteFactory
     * @param Registry $registry
     */
    public function __construct(
        SearchCriteriaBuilder     $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        WebsiteFactory            $websiteFactory,
        Registry                  $registry,
    )
    {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->websiteFactory = $websiteFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function aroundGetFormHtml(
        Main    $subject,
        Closure $proceed
    )
    {
        $form = $subject->getForm();
        if (is_object($form)) {

            $model = $this->_coreRegistry->registry('permissions_user');
            $data = $model->getData();

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
                    'value' => $data['user_type'] ?? '',
                    'options' => [
                        '0' => 'Usuario Administrador',
                        '1' => 'Usuario Sucursal (Gestor Bopis)'
                    ]
                ]
            );

            $sourceList = $this->getSourcesList();

            $options["all"] = "--Todas las sucursales--";
            foreach ($sourceList as $source) {
                $options[$source->getData("source_code")] = $source->getData("frontend_name");
            }

            $fieldset->addField(
                'source_code',
                'select',
                [
                    'name' => 'source_code',
                    'label' => __('Sucursal'),
                    'id' => 'source_code',
                    'title' => __('source_code_title'),
                    'required' => false,
                    'value' => $data['source_code'] ?? '',
                    'options' => $options
                ]
            );

            $websites = [0 => ""];
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
                    'value' => $data['website_id'] ?? '',
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
            ->addFilter("source_code", array('default', 'online_CO', 'online_CR', 'online_PE'), 'nin')
            ->create();
        $sourceData = $this->sourceRepository->getList($searchCriteria);
        return $sourceData->getItems();
    }
}
