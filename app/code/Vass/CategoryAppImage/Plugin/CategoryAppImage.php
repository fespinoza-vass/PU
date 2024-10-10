<?php

namespace Vass\CategoryAppImage\Plugin;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category\DataProvider;

class CategoryAppImage
{
    /**
     * Agregar el campo "category_app_image" en la respuesta de la API de categorías
     *
     * @param DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(DataProvider $subject, $result)
    {
        foreach ($result as $categoryId => $categoryData) {
            if (isset($categoryData['category']['category_app_image'])) {
                $result[$categoryId]['category_app_image'] = $categoryData['category']['category_app_image'];
            }
        }
        return $result;
    }

    /**
     * Agregar el campo "category_app_image" a los datos de la categoría en la API
     *
     * @param CategoryInterface $subject
     * @param array $result
     * @return array
     */
    public function afterGetDataById(CategoryInterface $subject, $result)
    {
        if ($subject->getCategoryAppImage()) {
            $result['category_app_image'] = $subject->getCategoryAppImage();
        }
        return $result;
    }
}
