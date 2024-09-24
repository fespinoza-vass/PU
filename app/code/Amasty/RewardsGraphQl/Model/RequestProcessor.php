<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class RequestProcessor
{
    /**
     * @var array
     */
    private $requestedFields = [];

    /**
     * Skip load data when request is not full
     *
     * @param String $field
     * @param ResolveInfo $info
     * @param int $depth
     * @return bool
     */
    public function isFieldRequested(String $field, ResolveInfo $info, int $depth = 10): bool
    {
        if (!$this->requestedFields) {
            $requestedFields = $info->getFieldSelection($depth);
            $this->requestedFields = $this->arrayWalker($requestedFields);
        }

        return in_array($field, $this->requestedFields, true);
    }

    /**
     * @param array $array
     * @param string $path
     * @return array
     */
    private function arrayWalker(array $array, string $path = ''): array
    {
        $parsedData = [];

        foreach ($array as $key => $fields) {
            $currentPath = $path . '/' . $key;
            $parsedData[] = trim($currentPath, '/');

            if (is_array($fields)) {
                $parsedData = array_merge_recursive($parsedData, $this->arrayWalker($fields, $currentPath));
            }
        }

        return $parsedData;
    }
}
