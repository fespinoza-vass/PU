<?php
namespace WolfSellers\Reports\Block\Adminhtml\Sales;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use WolfSellers\Reports\Helper\ColumnsFormat;

class GridCsv extends Template
{
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var MetadataProvider
     */
    private $metadataProvider;
    /**
     * @var int
     */
    private $pageSize;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $_directory;
    /**
     * @var ColumnsFormat
     */
    private $helper;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param ColumnsFormat $helper
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        ColumnsFormat $helper,
        $pageSize = 200
    ) {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
        $this->filter = $filter;
        $this->metadataProvider = $metadataProvider;
        $this->helper = $helper;
        $this->pageSize = $pageSize;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile(){
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);

        $this->_directory->create('export');
        $stream = $this->_directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $data = $dataProvider->getData();
        $totalCount = (int)$data['totalRecords'];
        while ($totalCount > 0) {
            $data = $dataProvider->getData();
            $items = $data['items'];
            foreach ($items as $item) {
                $row = [];
                foreach ($fields as $column) {
                    if (isset($item[$column])) {
                        $row[$column] = $this->helper->getColumnFormat($column, $item[$column]);
                    } else {
                        $row[] = '';
                    }
                }
                $stream->writeCsv($row);
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();
        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
