<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Controller\Adminhtml\Sequential;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use WolfSellers\Consecutive\Model\ConsecutiveRepository;

class InlineEdit implements ActionInterface
{

    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var ConsecutiveRepository
     */
    protected ConsecutiveRepository $consecutiveRepository;

    /**
     * @param JsonFactory $jsonFactory
     * @param RequestInterface $request
     * @param ConsecutiveRepository $consecutiveRepository
     */
    public function __construct(
        JsonFactory $jsonFactory,
        RequestInterface $request,
        ConsecutiveRepository $consecutiveRepository
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->consecutiveRepository = $consecutiveRepository;
    }

    /**
     * Inline edit action
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->request->getParam('isAjax')) {
            $postItems = $this->request->getParam('items', []);

            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelid) {
                    $consecutive = $this->consecutiveRepository->get($modelid);

                    try {
                        foreach ($postItems[$modelid] as $key => $value) {
                            $consecutive->setData($key, $value);
                        }

                        $this->consecutiveRepository->save($consecutive);
                    } catch (\Exception $e) {
                        $messages[] = "[Sequential ID: {$modelid}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}

