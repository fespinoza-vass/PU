<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Test\GraphQl;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class GetExpiringDataTest extends GraphQlAbstract
{
    public const REWARD_CUSTOMER = 'rewardspoints@amasty.com';
    public const REWARD_CUSTOMER_PASS = 'rewardspassword';
    public const MAIN_QUERY_KEY = 'getExpiringData';

    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerTokenService = Bootstrap::getObjectManager()->get(CustomerTokenServiceInterface::class);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_order_complete.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_newsletter.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_rule_order_complete.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_expired_points.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_expiring_points.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_rule_newsletter.php
     */
    public function testGetExpiringData()
    {
        $fields = [
            'expiring_data' => [
                [
                    'amount' => '7',
                    'expiration_date' => date('Y-m-d', strtotime('now + 2 days'))
                ],
                [
                    'amount' => '5',
                    'expiration_date' => date('Y-m-d', strtotime('now + 10 days'))
                ]
            ]
        ];

        $query = $this->getQuery();
        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_QUERY_KEY], $fields);
    }

    /**
     * @param string $userName
     * @param string $password
     *
     * @return string[]
     * @throws AuthenticationException
     */
    private function getHeader(
        string $userName = self::REWARD_CUSTOMER,
        string $password = self::REWARD_CUSTOMER_PASS
    ): array {
        $customerToken = $this->customerTokenService->createCustomerAccessToken($userName, $password);
        return ['Authorization' => 'Bearer ' . $customerToken];
    }

    /**
     * @return string
     */
    private function getQuery(): string
    {
        $query = <<<QUERY
{
    getExpiringData {
        expiring_data {
            amount
            expiration_date
        }
    }
}
QUERY;
        return $query;
    }
}
