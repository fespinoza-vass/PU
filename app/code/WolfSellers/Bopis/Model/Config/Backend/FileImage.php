<?php
 
namespace WolfSellers\Bopis\Model\Config\Backend;
 
class FileImage extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * Allow MP3 files
     *
     * @return string[]
     */
    public function getAllowedExtensions() {
        return ['png','jpg','ico','jpeg'];
    }
}