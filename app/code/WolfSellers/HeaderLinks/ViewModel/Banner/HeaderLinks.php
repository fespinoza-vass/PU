<?php

declare(strict_types=1);

namespace WolfSellers\HeaderLinks\ViewModel\Banner;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use WolfSellers\HeaderLinks\Model\ConfigProvider;

class HeaderLinks implements ArgumentInterface
{
    /** @var int */
    const LIMIT_BUTTONS = 3;

    /**
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        protected ConfigProvider $configProvider
    )
    {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configProvider->isEnabled();
    }

    /**
     * @return array
     */
    public function getButtons(): array
    {
        $buttons = [];

        for ($limit = 1; $limit <= self::LIMIT_BUTTONS; $limit++) {

            if (!$this->configProvider->call('isButton' . $limit . 'Visible')) {
                continue;
            }

            $nameFunction = 'getButton' . $limit . 'Name';
            $urlFunction = 'getButton' . $limit . 'Url';
            $contentFunction = 'getButton' . $limit . 'Content';

            $buttons[$limit] = [
                'name' => $this->configProvider->call($nameFunction),
                'link' => $this->configProvider->call($urlFunction),
                'content' => $this->configProvider->call($contentFunction)
            ];
        }
        return $buttons ?? [];
    }
}
