<?php
declare(strict_types=1);

namespace PechoSolutions\Visanet\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\SessionStartChecker;
class TransparentSessionChecker
{
    const TRANSPARENT_REDIRECT_PATH = 'visanet/visa/web/';

    /**
     * @var Http
     */
    private $request;

    /**
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }


    /**
     * @param SessionStartChecker $subject
     * @param bool $result
     * @return bool
     */
    public function afterCheck(SessionStartChecker $subject, bool $result): bool
    {
        if ($result === false) {
            return false;
        }
        return strpos((string)$this->request->getPathInfo(), self::TRANSPARENT_REDIRECT_PATH) === false;
    }
}