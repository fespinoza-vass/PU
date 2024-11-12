<?php
declare(strict_types=1);

namespace Vass\FixProductGrid\Plugin\Frontend;

use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Psr\Log\LoggerInterface;

class ProductsListPlugin
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Plugin 'before' to reset order to default before create collection
     *
     * @param ProductsList $subject
     */
    public function beforeCreateCollection(ProductsList $subject): void
    {
        $data = $subject->getData();

        if (isset($data['sort_order']) && is_string($data['sort_order'])) {
            switch ($data['sort_order']) {
                case 'date_newest_top':
                    $subject->setData('default_sort_order', 'DESC');
                    break;
                case 'date_oldest_top':
                    $subject->setData('default_sort_order', 'ASC');
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Plugin 'after' to reorder the product collection by entity id
     *
     * @param ProductsList $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterCreateCollection(ProductsList $subject, Collection $result): Collection
    {
        $data = $subject->getData();
       
        // Validate that 'sort_order' is set and is a valid string
        if (isset($data['sort_order']) && is_string($data['sort_order'])) {
            try {
                switch ($data['sort_order']) {
                    case 'date_newest_top':
                        $result->getSelect()->reset('order');
                        $result->getSelect()->order('entity_id DESC');
                        break;
                    case 'date_oldest_top':
                        $result->getSelect()->reset('order');
                        $result->getSelect()->order('entity_id ASC');
                        break;
                    default:
                        break; // Do nothing if it's not a valid sort order
                }
            } catch (\Exception $e) {
                // Log the exception and continue without interrupting execution
                $this->logger->error('Error reordering product collection: ' . $e->getMessage());
            }
        }
        return $result;
    }
}