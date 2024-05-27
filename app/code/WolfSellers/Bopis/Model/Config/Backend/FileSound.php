<?php
 
namespace WolfSellers\Bopis\Model\Config\Backend;
 
class FileSound extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * Allow MP3 files
     *
     * @return string[]
     */
    public function getAllowedExtensions() {
        return ['mp3'];
    }
}