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
    const DEFAULT_CORRELATIVE_ID = 'textinput-1650406602047';

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
     * @param \Amasty\Customform\Controller\Form\Submit $subject
     * @return \Amasty\Customform\Controller\Form\Submit
     */
    public function beforeExecute(
        \Amasty\Customform\Controller\Form\Submit $subject
    )
    {
        $subject->getRequest()->setPostValue(self::DEFAULT_CORRELATIVE_ID, 'processing');
        return $subject;
    }
}
