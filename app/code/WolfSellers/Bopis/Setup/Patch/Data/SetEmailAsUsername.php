<?php

namespace WolfSellers\Bopis\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\User\Model\User;

class SetEmailAsUsername implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private ModuleDataSetupInterface $moduleDataSetup;

    /** @var UserCollectionFactory */
    private UserCollectionFactory $userCollectionFactory;

    /** @var User */
    private User $_user;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param UserCollectionFactory $userCollection
     * @param User $user
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        UserCollectionFactory    $userCollection,
        User                     $user
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->userCollectionFactory = $userCollection;
        $this->_user = $user;
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $users = $this->userCollectionFactory->create();

        foreach ($users as $user) {
            $currentUser = $this->_user->loadByUsername($user->getUserName());
            $currentUser->setUserName($user->getEmail());
            $currentUser->save();
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
