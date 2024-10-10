<?php

namespace Vass\CategoryAppImage\Plugin;

use Magento\Framework\Exception\LocalizedException;

class ValidateImage
{
    public function beforeSave(\Magento\Catalog\Model\Category\Attribute\Backend\Image $subject, $object)
    {
        $image = $object->getData('category_app_image');
        if ($image && isset($image[0]['size'])) {
            $fileSize = $image[0]['size'];
            $maxFileSize = 4194304; // 4 MB en bytes
            if ($fileSize > $maxFileSize) {
                throw new LocalizedException(__('The uploaded file exceeds the maximum allowed size of 4MB.'));
            }
        }
    }
}
