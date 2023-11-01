<?php

namespace WolfSellers\Bopis\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Psr\Log\LoggerInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class SourceOptions implements ArrayInterface
{

    /**
     * @param SourceRepositoryInterface $_sourceRepository
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     * @param AuthSession $authSession
     * @param LoggerInterface $logger
     * @param UserCollectionFactory $userCollectionFactory
     */
    public function __construct(
        protected SourceRepositoryInterface $_sourceRepository,
        protected SearchCriteriaBuilder     $_searchCriteriaBuilder,
        protected AuthSession               $authSession,
        protected LoggerInterface           $logger,
        protected UserCollectionFactory     $userCollectionFactory
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $sourceCode = $this->authSession->getUser()->getData('source_code');
        $roleName = $this->getUserRole($this->authSession->getUser());

        if ($sourceCode != 'all' && $roleName !== AbstractBopisCollection::BOPIS_SUPER_ADMIN ) {
            $this->_searchCriteriaBuilder->addFilter('source_code', $sourceCode);
        }

        $this->_searchCriteriaBuilder->addFilter('enabled', true);
        $searchCriteria = $this->_searchCriteriaBuilder->create();

        $searchCriteriaResult = $this->_sourceRepository->getList($searchCriteria);
        $sources = $searchCriteriaResult->getItems();
        $options = [];

        try {
            foreach ($sources as $source) {
                $sourceCode = trim($source->getSourceCode());
                $options[$sourceCode] = $source->getName();
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        $options[''] = 'Todo';

        return $options;
    }

    /**
     * @param \Magento\User\Model\User $user
     * @return mixed|null
     */
    private function getUserRole(\Magento\User\Model\User $user)
    {
        $collection = $this->userCollectionFactory->create();
        $collection->addFieldToFilter('main_table.user_id', $user->getId());
        $userData = $collection->getFirstItem();
        return $userData->getDataByKey('role_name');
    }
}
