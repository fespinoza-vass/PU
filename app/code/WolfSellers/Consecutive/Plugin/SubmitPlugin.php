<?php

namespace WolfSellers\Consecutive\Plugin;

use Amasty\Customform\Api\Data\AnswerInterface;
use Amasty\Customform\Model\Answer;
use Amasty\Customform\Model\Form;
use Amasty\Customform\Model\Submit;
use WolfSellers\Consecutive\Model\ConsecutiveBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use WolfSellers\Consecutive\Logger\Logger;
use Exception;

class SubmitPlugin
{
    const DEFAULT_STORE = 1;

    const DEFAULT_CORRELATIVE_ID = 'textinput-1650406602047';

    const NUMBER_NAME = 'Número Correlativo';

    /** @var ConsecutiveBuilder */
    protected ConsecutiveBuilder $_consecutiveBuilder;

    /** @var Json */
    protected Json $_json;

    /** @var Logger */
    protected Logger $logger;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(
        ConsecutiveBuilder $consecutiveBuilder,
        Json               $json,
        Logger             $logger,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        $this->_consecutiveBuilder = $consecutiveBuilder;
        $this->_json = $json;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param Submit $subject
     * @param Answer $result
     * @param Form $formModel
     * @param AnswerInterface|null $answer
     * @return Answer
     * @throws Exception
     */
    public function afterSubmit(Submit $subject, Answer $result, Form $formModel, ?AnswerInterface $answer = null): Answer
    {

        $model = $result;
        $codeForm = $result->getFormCode();
        /*RC Resource Connection*/
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('amasty_customform_answer');
        $sql = "SELECT * FROM amasty_customform_answer WHERE form_code LIKE '$codeForm' and correlative_number is not null ORDER BY answer_id DESC";
        $this->logger->error($sql);
        $result_query = $connection->fetchAll($sql);
        $count_result = 0;
        if(count($result_query) > 0) {
            $this->logger->error(print_r($result_query[0], true));
            $count_result = (int)str_replace("", $codeForm, $result_query[0]['correlative_number']);
            $this->logger->error("\$count_result: " . $count_result);
        }
        $consecutive = $this->_consecutiveBuilder->getNewConsecutiveToAssign(self::DEFAULT_STORE);
        //$correlative = $consecutive['consecutive_name'];
        $new_correlative_number = str_replace("LRV","",$consecutive['consecutive_name']);
        $longitudeCorrelative = strlen(str_replace("LRV","",$consecutive['consecutive_name']));
        $correlative = $codeForm . str_pad(($count_result + 1),10,0,STR_PAD_LEFT);
        $this->logger->error("correlative: " . $correlative);
        $model->setData('correlative_number', $correlative);

        $json = $model->getResponseJson();
        $data = $this->_json->unserialize($json);

        if (!isset($data[self::DEFAULT_CORRELATIVE_ID])) {
            $node[self::DEFAULT_CORRELATIVE_ID] = [
                "label" => self::NUMBER_NAME,
                "type" => "textinput"
            ];
            $data = array_merge($node, $data);
        }

        $data[self::DEFAULT_CORRELATIVE_ID]['value'] = $correlative;

        $model->setResponseJson($this->_json->serialize($data));

        $this->logger->info('::: correlative to row :::', [$model->getAnswerId() => $correlative]);

        try {
            $model->save();
        } catch (Exception $e) {
            $this->logger->error('AFTER_SUBMIT:' . $e->getMessage());
        }

        return $result;
    }
}
