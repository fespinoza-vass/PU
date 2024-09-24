<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Ui\Component\Listing\Column;

use Amasty\Base\Model\MagentoVersion;
use Amasty\Rewards\Api\CustomerBalanceRepositoryInterface;
use Magento\Customer\Ui\Component\ColumnFactory;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\Customer\Ui\Component\Listing\Column\InlineEditUpdater;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class PointsColumn extends \Magento\Customer\Ui\Component\Listing\Columns
{
    public const FIELD_NAME = 'amount';

    /**
     * @var CustomerBalanceRepositoryInterface
     */
    private $customerBalanceRepository;

    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    public function __construct(
        ContextInterface $context,
        ColumnFactory $columnFactory,
        AttributeRepository $attributeRepository,
        InlineEditUpdater $inlineEditor,
        CustomerBalanceRepositoryInterface $customerBalanceRepository,
        array $components = [],
        array $data = [],
        array $filterConfigProviders = [],
        MagentoVersion $magentoVersion = null // TODO move to not optional
    ) {
        $this->magentoVersion = $magentoVersion ?? ObjectManager::getInstance()->get(MagentoVersion::class);

        if (version_compare($this->magentoVersion->get(), '2.4.4', '<=')) {
            parent::__construct(
                $context,
                $columnFactory,
                $attributeRepository,
                $inlineEditor,
                $components,
                $data
            );
        } else {
            parent::__construct(
                $context,
                $columnFactory,
                $attributeRepository,
                $inlineEditor,
                $components,
                $data,
                $filterConfigProviders
            );
        }
        $this->customerBalanceRepository = $customerBalanceRepository;
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $rewardBalance = $this->customerBalanceRepository->getBalanceByCustomerId((int)$item["entity_id"]);
                $item[self::FIELD_NAME] = $rewardBalance;
            }
        }

        return $dataSource;
    }
}
