<?php

namespace WolfSellers\Reports\Block\Adminhtml\Sales;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Ui\Model\Export\SearchResultIteratorFactory;
use WolfSellers\Reports\Helper\ColumnsFormat;

class GridXml extends Template{
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
     * @var ExcelFactory
     */
    private $excelFactory;
    /**
     * @var SearchResultIteratorFactory
     */
    private $iteratorFactory;
    /**
     * @var ColumnsFormat
     */
    private $helper;
    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param ExcelFactory $excelFactory
     * @param SearchResultIteratorFactory $iteratorFactory
     * @param ColumnsFormat $helper
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        ExcelFactory $excelFactory,
        SearchResultIteratorFactory $iteratorFactory,
        ColumnsFormat $helper,
        $pageSize = 200
    ) {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
        $this->filter = $filter;
        $this->metadataProvider = $metadataProvider;
        $this->excelFactory = $excelFactory;
        $this->iteratorFactory = $iteratorFactory;
        $this->helper = $helper;
        $this->pageSize = $pageSize;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * Returns XML file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getXmlFile()
    {
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.xml';

        $this->filter->prepareComponent($component);

        $dataProvider = $component->getContext()->getDataProvider();

        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize(200);
        $data = $dataProvider->getData();
        $fields = $this->metadataProvider->getFields($component);
        $totalCount = (int) $data['totalRecords'];
        $searchResult = [];
        while ($totalCount > 0) {
            $data = $dataProvider->getData();
            foreach ($data['items'] as &$item) {
                $row = [];
                foreach ($fields as $column) {
                    if (isset($item[$column])) {
                        $row[$column] = $this->helper->getColumnFormat($column, $item[$column]);
                    } else {
                        $row[$column] = '';
                    }
                }
                $item = $row;
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - 200;
            $searchResult = array_merge($searchResult,$data['items']);
        }

        $searchResultIterator = $this->iteratorFactory->create(['items' => $searchResult]);

        $excel = $this->excelFactory->create(
            [
                'iterator' => $searchResultIterator
            ]
        );

        $this->_directory->create('export');
        $stream = $this->_directory->openFile($file, 'w+');
        $stream->lock();

        $excel->setDataHeader($this->metadataProvider->getHeaders($component));
        $excel->write($stream, $component->getName() . '.xml');

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
