<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Plugin;

use Amasty\Customform\Api\AnswerRepositoryInterface;
use Amasty\Customform\Api\Data\AnswerInterface;
use Amasty\Customform\Model\Answer;
use WolfSellers\Consecutive\Model\ConsecutiveBuilder;
use WolfSellers\Consecutive\Logger\Logger;

class AddCorrelativeNumberToAnswerPlugin
{

    /** @var ConsecutiveBuilder */
    protected ConsecutiveBuilder $_consecutiveBuilder;

    /** @var Answer */
    protected Answer $_answer;

    /** @var Logger */
    protected Logger $logger;

    public function __construct(
        ConsecutiveBuilder $consecutiveBuilder,
        Answer             $answer,
        Logger             $logger
    )
    {
        $this->_consecutiveBuilder = $consecutiveBuilder;
        $this->_answer = $answer;
        $this->logger = $logger;
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

            if (empty($myAnswer->getData('correlative_number'))) {
                $json = $myAnswer->getResponseJson();
                $data = json_decode($json, true);

                $correlative = $data[\WolfSellers\Consecutive\Plugin\AddCorrelativeNumberPlugin::DEFAULT_CORRELATIVE_ID]['value'];

                $myAnswer->setData('correlative_number', $correlative);
                $myAnswer->save();

                $this->logger->info('::: adding correlative to post :::', [$entity->getAnswerId() => $correlative]);
            }
        }catch (\Exception $e){
            $this->logger->error($e->getMessage());
        }
        return $entity;
    }
}
