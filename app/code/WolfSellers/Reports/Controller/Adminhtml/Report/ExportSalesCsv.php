<?php

namespace WolfSellers\Reports\Controller\Adminhtml\Report;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class ExportSalesCsv extends Action implements HttpGetActionInterface
{
    /**
     * @var FileFactory
     */
    private $_fileFactory;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Export products report grid to CSV format
     *
     * @return ResponseInterface
     * @throws Exception
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function execute()
    {
        $fileName = 'pu_report_sales.csv';
        $content = $this->_view->getLayout()->createBlock(
            \WolfSellers\Reports\Block\Adminhtml\Sales\GridCsv::class
        )->getCsvFile();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
