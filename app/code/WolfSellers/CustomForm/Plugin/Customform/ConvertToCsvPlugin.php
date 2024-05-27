<?php
declare(strict_types=1);

namespace WolfSellers\CustomForm\Plugin\Customform;

use WolfSellers\CustomForm\Logger\Logger;
use Magento\Framework\File\Csv;
use Amasty\Customform\Model\ResourceModel\Form\Collection as Forms;
use Magento\Framework\Serialize\Serializer\Json;

class ConvertToCsvPlugin extends \Magento\Ui\Model\Export\ConvertToCsv
{
    const ARRAY_POSITION_JSON = 8;

    protected array $headersWhitAdditional;
    protected array $mainAditional;

    /** @var Logger */
    protected Logger $logger;

    /** @var Csv */
    protected Csv $csv;

    /** @var Forms */
    protected Forms $forms;

    /** @var Json */
    protected Json $json;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Amasty\Customform\Model\Export\MetadataProvider $metadataProvider,
        Logger $logger,
        Csv $csv,
        Forms $forms,
        Json $json
    ) {
        $this->logger = $logger;
        $this->csv = $csv;
        $this->forms = $forms;
        $this->headersWhitAdditional = [];
        $this->mainAditional = [];
        $this->json = $json;
        parent::__construct($filesystem, $filter, $metadataProvider);
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function afterGetCsvFile(
        \Amasty\Customform\Model\Export\ConvertToCsv $subject,
                                                     $result
    )
    {
        if (!isset($result['value'])) {
            $this->logger->info('No existe archivo a parsear');
            return $result;
        }

        try {
            $file = $this->directory->getAbsolutePath().$result['value'];
            $csv = $this->csv->getData($file);

            if (!count($csv)){
                $this->logger->info('Sin contenido al parsear');
                return $result;
            }

            $this->getAdditionalHeaders($csv);
            $parsedFile = $this->parseData($csv);
            $this->directory->delete($file);

            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $stream->writeCsv($this->headersWhitAdditional);
            foreach ($parsedFile as $document){
                $stream->writeCsv($document);
            }
            $stream->unlock();
            $stream->close();
        }catch (\Exception $e){
            $this->logger->error($e->getMessage() . "\n" . $e->getTraceAsString());
            $this->logger->info('error', $document);
        }

        return $result;
    }

    private function getAdditionalHeaders($cvs)
    {
        $headers = $original = array_shift($cvs);
        array_push($headers, '***');

        foreach ($cvs as $items){
            $answer = $this->json->unserialize($items[self::ARRAY_POSITION_JSON]);
            foreach ($answer as $input){
                if (key($input)){
                    if (!in_array(key($input), $headers)){
                        array_push($headers, key($input));
                    }
                }
            }
        }
        $this->headersWhitAdditional = $headers;
        $this->setmainAditional($original);
    }

    private function parseData($csv)
    {
        array_shift($csv);
        $parseData=[];
        foreach ($csv as $answer){
            $mainAdditional = $this->mainAditional;

            if ($answer[self::ARRAY_POSITION_JSON] === null || $answer[self::ARRAY_POSITION_JSON] === ''){
                $ans_inputs = [];
            }else{
                $ans_inputs = $this->json->unserialize($answer[self::ARRAY_POSITION_JSON]);
            }

            foreach ($ans_inputs as $node){
                $mainAdditional[key($node)] = current($node);
            }

            foreach ($mainAdditional as $key => $value){
                array_push($answer, $value);
            }
            array_push($parseData, $answer);
        }

        return $parseData;
    }

    private function setmainAditional($original)
    {
        foreach ($this->headersWhitAdditional as $header){
            if (!in_array($header, $original)){
                $additional[$header] = '';
            }
        }
        $this->mainAditional = $additional??[];
    }

}

