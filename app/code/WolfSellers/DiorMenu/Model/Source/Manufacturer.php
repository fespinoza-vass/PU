<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-05-02
 * Time: 21:49
 */

declare(strict_types=1);

namespace WolfSellers\DiorMenu\Model\Source;

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Manufacturer options.
 */
class Manufacturer implements OptionSourceInterface
{
    public const ATTR_CODE = 'manufacturer';

    /** @var Repository */
    private Repository $productAttributeRepository;

    /**
     * @param Repository $productAttributeRepository
     */
    public function __construct(Repository $productAttributeRepository)
    {
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        $options = [];

        $manufacturerAttr = $this->productAttributeRepository->get(self::ATTR_CODE);

        foreach ($manufacturerAttr->getOptions() as $option) {
            $options[] = [
                'value' => $option->getValue(),
                'label' => $option->getLabel(),
            ];
        }

        return $options;
    }
}
