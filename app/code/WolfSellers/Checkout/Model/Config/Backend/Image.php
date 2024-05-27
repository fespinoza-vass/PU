<?php
namespace WolfSellers\Checkout\Model\Config\Backend;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

class Image extends \Magento\Config\Model\Config\Backend\Image
{
    const UPLOAD_DIR = 'theme_customization';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir(): string
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * @return bool
     */
    protected function _addWhetherScopeInfo(): bool
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return string[]
     */
    protected function _getAllowedExtensions(): array
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }

    /**
     * @return mixed|null
     */
    protected function getTmpFileName(): mixed
    {
        if (isset($_FILES['groups'])) {
            return $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
        }
        return is_array($this->getValue()) ? $this->getValue()['tmp_name'] : null;
    }

    /**
     * @return Image
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function beforeSave(): Image
    {
        $value = $this->getValue();
        $deleteFlag = is_array($value) && !empty($value['delete']);
        if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value))
        {
            $fileTmpName = $this->getTmpFileName();
            if ($this->getOldValue() && ($fileTmpName || $deleteFlag))
            {
                $this->_mediaDirectory->delete(self::UPLOAD_DIR . '/' . $this->getOldValue());
            }
        }
        return parent::beforeSave();
    }

    /**
     * @param $value
     * @return bool
     */
    private function isTmpFileAvailable($value): bool
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }

    /**
     * @param $value
     * @return mixed|string
     */
    private function getUploadedImageName($value): mixed
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }
        return '';
    }
}
