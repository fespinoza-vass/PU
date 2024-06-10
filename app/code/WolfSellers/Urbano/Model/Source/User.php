<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-07-15
 * Time: 17:17
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Model\Source;

use Magento\Framework\Data\Collection as CollectionAlias;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\User\Model\ResourceModel\User\Collection;

/**
 * Config source User.
 */
class User implements OptionSourceInterface
{
    /** @var Collection */
    private Collection $collection;

    /**
     * Constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function toOptionArray(): array
    {
        $options = [];
        $options[] = [
            'value' => '',
            'label' => __('--Please Select--'),
        ];

        $users = $this->collection
            ->addOrder('firstname', CollectionAlias::SORT_ORDER_ASC)
            ->addOrder('lastname', CollectionAlias::SORT_ORDER_ASC)
            ->loadData();

        /** @var \Magento\User\Model\User $user */
        foreach ($users as $user) {
            $options[] = [
                'value' => $user->getId(),
                'label' => $user->getName().' ('.$user->getId().')',
            ];
        }

        return $options;
    }
}
