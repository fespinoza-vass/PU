<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Test\GraphQl;

use Magento\TestFramework\TestCase\GraphQlAbstract;

class GetRewardsPropertyTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'getRewardsProperty';

    /**
     * @group amasty_rewards
     *
     * @magentoConfigFixture currency/options/base USD
     * @magentoConfigFixture currency/options/default CNY
     * @magentoConfigFixture currency/options/allow CNY,USD
     *
     * @magentoDataFixture Magento/Directory/_files/usd_cny_rate.php
     */
    public function testGetRewardsPropertyUAH()
    {
        $fields = [
            'current_currency_code' => 'CNY',
            'points_rate' => 7
        ];

        $query = $this->getQuery();
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_QUERY_KEY], $fields);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoConfigFixture currency/options/base USD
     */
    public function testGetRewardsProperty()
    {
        $fields = [
            'current_currency_code' => 'USD',
            'points_rate' => 1
        ];

        $query = $this->getQuery();
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_QUERY_KEY], $fields);
    }

    /**
     * @return string
     */
    private function getQuery(): string
    {
        $query = <<<QUERY
{
    getRewardsProperty {
        current_currency_code
        points_rate
    }
}
QUERY;
        return $query;
    }
}
