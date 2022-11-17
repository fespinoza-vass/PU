<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Plugin;

use Amasty\Customform\Api\AnswerRepositoryInterface;
use Amasty\Customform\Api\Data\AnswerInterface;
use Amasty\Customform\Model\Answer;
use WolfSellers\Consecutive\Model\ConsecutiveBuilder;
use WolfSellers\Consecutive\Logger\Logger;
use Magento\Framework\Serialize\Serializer\Json;

class AddCorrelativeNumberToAnswerPlugin
{
    const DEFAULT_STORE = 1;

    const NUMBER_NAME = 'Número Correlativo';

    /** @var ConsecutiveBuilder */
    protected ConsecutiveBuilder $_consecutiveBuilder;

    /** @var Answer */
    protected Answer $_answer;

    /** @var Logger */
    protected Logger $logger;

    /** @var Json */
    protected Json $json;

    public function __construct(
        ConsecutiveBuilder $consecutiveBuilder,
        Answer             $answer,
        Logger             $logger,
        Json               $json
    )
    {
        $this->_consecutiveBuilder = $consecutiveBuilder;
        $this->_answer = $answer;
        $this->logger = $logger;
        $this->json = $json;
    }

    /**
     * @param AnswerRepositoryInterface $subject
     * @param AnswerInterface           $entity
     * @param                           $result
     * @return AnswerInterface
     * @throws \Exception
     */
    public function afterSave(
        AnswerRepositoryInterface $subject,
        AnswerInterface           $entity,
                                  $result
    )
    {
        try {
            $myAnswer = $this->_answer->load($entity->getAnswerId());

            if (!$myAnswer) return $entity;

            $consecutive = $this->_consecutiveBuilder->getNewConsecutiveToAssign(self::DEFAULT_STORE);
            $correlative = $consecutive['consecutive_name'];

            $myAnswer->setData('correlative_number', $correlative);

            $json = $myAnswer->getResponseJson();
            $data = $this->json->unserialize($json);

            if (!isset($data[\WolfSellers\Consecutive\Plugin\AddCorrelativeNumberPlugin::DEFAULT_CORRELATIVE_ID])){
                $data[\WolfSellers\Consecutive\Plugin\AddCorrelativeNumberPlugin::DEFAULT_CORRELATIVE_ID] = [
                    "label"=> self::NUMBER_NAME,
                    "type"=> "textinput"
                ];
            }

            $data[\WolfSellers\Consecutive\Plugin\AddCorrelativeNumberPlugin::DEFAULT_CORRELATIVE_ID]['value'] = $correlative;

            $myAnswer->setResponseJson(
                $this->json->serialize($data)
            );

            $myAnswer->save();
            $this->logger->info('::: correlative to row :::', [$entity->getAnswerId() => $correlative]);

        }catch (\Exception $e){
            $this->logger->error($e->getMessage());
        }
        return $entity;
    }
}
