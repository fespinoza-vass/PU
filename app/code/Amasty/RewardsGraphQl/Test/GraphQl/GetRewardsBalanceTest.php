<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Test\GraphQl;

use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class GetRewardsBalanceTest extends GraphQlAbstract
{
    public const REWARD_CUSTOMER = 'rewardspoints@amasty.com';
    public const REWARD_CUSTOMER_PASS = 'rewardspassword';

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
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_every_spent.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_admin.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_rule_order_complete.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_rule_every_spent.php
     */
    public function testGetCustomerRewardsBalance()
    {
        $query = $this->getQueryBalance();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertEquals('35', $response['rewards']['balance']);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     */
    public function testGetCustomerRewardsZeroBalance()
    {
        $query = $this->getQueryBalance();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertEquals('0', $response['rewards']['balance']);
    }

    /**
     * @param string $userName
     * @param string $password
     *
     * @return string[]
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
    private function getQueryBalance(): string
    {
        $query = <<<QUERY
{
    rewards {
        balance
    }
}
QUERY;
        return $query;
    }
}
