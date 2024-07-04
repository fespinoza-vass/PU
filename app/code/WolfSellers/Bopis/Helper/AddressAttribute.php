<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Customer\Model\FileProcessorFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;

class AddressAttribute extends AbstractHelper
{
    private Url $url;
    private StoreManagerInterface $storeManager;
    private Filesystem $filesystem;
    private FileProcessorFactory $fileProcessorFactory;
    private Mime $mime;
    private File $ioFile;
    private string $uploadUrl;
    private string $entityType;
    private Filesystem\Directory\WriteInterface $mediaDirectory;

    public function __construct(
        Context $context,
        Url $url,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        FileProcessorFactory $fileProcessorFactory,
        Mime $mime,
        File $ioFile
    )
    {
        parent::__construct($context);
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->fileProcessorFactory = $fileProcessorFactory;
        $this->mime = $mime;
        $this->ioFile = $ioFile;
        $this->uploadUrl = "customer_custom_attributes/address_file/upload";
        $this->entityType = "customer_address";
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function getJsComponentsDefinitions(
        array $userAttributes,
        AbstractModel $entity
    ): string {
        $result = [];
        foreach ($userAttributes as $attribute) {
            $config = [];
            $frontendInput = $attribute->getFrontendInput();

            if (in_array($frontendInput, ['file', 'image'])) {
                $config['component'] = 'Magento_CustomerCustomAttributes/js/component/file-uploader';
                $config['template'] = 'Magento_CustomerCustomAttributes/form/element/uploader/uploader';
                $config['label'] = $attribute->getDefaultFrontendLabel();
                $config['formElement'] = 'fileUploader';
                $config['componentType'] = 'fileUploader';
                $config['uploaderConfig'] = [
                    'url' => $this->url->getUrl(
                        $this->uploadUrl
                    )
                ];

                $config['dataScope'] = $attribute->getAttributeCode();

                $filename = $entity->getData($attribute->getAttributeCode());

                if ($filename) {
                    $filePath = $this->entityType . $filename;
                    $fileProcessor = $this->fileProcessorFactory
                        ->create(['entityTypeCode' => $this->entityType]);
                    $fileInfo = $this->mediaDirectory->stat($filePath);
                    $fileAbsolutePath = $this->mediaDirectory->getAbsolutePath() . $filePath;
                    $config['value'] = [
                        [
                            'file' => $filename,
                            'name' => $this->ioFile->getPathInfo($filename)['basename'],
                            'size' => $fileInfo['size'],
                            'url' => $fileProcessor->getViewUrl($filename, 'file'),
                            'type' => $this->mime->getMimeType($fileAbsolutePath),
                        ]
                    ];
                }

                if ($attribute->getIsRequired()) {
                    $config['validation'] = [
                        'required' => true,
                    ];
                    $config['required'] = '1';
                }
            }

            $result[$attribute->getAttributeCode()] = $config;
        }

        return json_encode($result);
    }
}
