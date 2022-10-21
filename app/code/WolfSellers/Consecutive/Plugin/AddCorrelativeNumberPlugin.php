<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Plugin;

use Amasty\Customform\Api\AnswerRepositoryInterface;
use Amasty\Customform\Api\Data\AnswerInterface;
use Amasty\Customform\Model\Answer;
use WolfSellers\Consecutive\Model\ConsecutiveBuilder;
use WolfSellers\Consecutive\Logger\Logger;

class AddCorrelativeNumberPlugin
{
    const DEFAULT_STORE = 1;

    /** @var ConsecutiveBuilder  */
    protected ConsecutiveBuilder $_consecutiveBuilder;

    /** @var Answer  */
    protected Answer $_answer;

    /** @var Logger  */
    protected Logger $logger;

    public function __construct(
        ConsecutiveBuilder $consecutiveBuilder,
        Answer $answer,
        Logger $logger
    ){
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
        AnswerInterface $entity,
        $result
    ) {
        $this->logger->info('Validando la existencia de número correlativo', ['AnswerId'=>$entity->getAnswerId()]);
        $myAnswer = $this->_answer->load($entity->getAnswerId());

        if (!$myAnswer) return $entity;

        if (empty($myAnswer->getData('correlative_number'))){
            $consecutive = $this->_consecutiveBuilder->getNewConsecutiveToAssign(self::DEFAULT_STORE);

            $this->logger->info('Creando número correlativo', ['AnswerId'=>$consecutive['consecutive_name']]);
            $myAnswer->setData('correlative_number',$consecutive['consecutive_name']);
            $myAnswer->save();
        }

        return $entity;
    }
}
