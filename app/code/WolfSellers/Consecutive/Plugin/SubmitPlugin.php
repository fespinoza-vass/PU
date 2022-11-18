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

    public function __construct(
        ConsecutiveBuilder $consecutiveBuilder,
        Json               $json,
        Logger             $logger

    )
    {
        $this->_consecutiveBuilder = $consecutiveBuilder;
        $this->_json = $json;
        $this->logger = $logger;
    }

    /**
     * @param Submit               $subject
     * @param Answer               $result
     * @param Form                 $formModel
     * @param AnswerInterface|null $answer
     * @return Answer
     * @throws Exception
     */
    public function afterSubmit(Submit $subject, Answer $result, Form $formModel, ?AnswerInterface $answer = null): Answer
    {

        $model = $result;

        $consecutive = $this->_consecutiveBuilder->getNewConsecutiveToAssign(self::DEFAULT_STORE);
        $correlative = $consecutive['consecutive_name'];

        $model->setData('correlative_number', $correlative);

        $json = $model->getResponseJson();
        $data = $this->_json->unserialize($json);

        if (!isset($data[self::DEFAULT_CORRELATIVE_ID])){
            $node[self::DEFAULT_CORRELATIVE_ID] = [
                "label"=> self::NUMBER_NAME,
                "type"=> "textinput"
            ];
            $data = array_merge($node, $data);
        }

        $data[self::DEFAULT_CORRELATIVE_ID]['value'] = $correlative;

        $model->setResponseJson( $this->_json->serialize($data));

        $this->logger->info('::: correlative to row :::', [$model->getAnswerId() => $correlative]);

        try {
            $model->save();
        }catch (Exception $e){
            $this->logger->error('AFTER_SUBMIT:' . $e->getMessage());
        }

        return $result;
    }
}