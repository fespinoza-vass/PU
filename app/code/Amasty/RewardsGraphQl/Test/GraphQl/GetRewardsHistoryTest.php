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

class GetRewardsHistoryTest extends GraphQlAbstract
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
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     */
    public function testGetCustomerRewardsHistoryNotItems()
    {
        $query = $this->getQueryHistory();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertEquals('0', $response['rewards']['history']['total_count']);
        $this->assertIsArray($response['rewards']['history']['items']);
        $this->assertEmpty($response['rewards']['history']['items']);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_newsletter.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_rule_newsletter.php
     */
    public function testGetCustomerRewardsHistoryRuleItem()
    {
        $query = $this->getQueryHistory();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertEquals('1', $response['rewards']['history']['total_count']);
        $this->assertIsArray($response['rewards']['history']['items']);
        $this->assertNotEmpty($response['rewards']['history']['items']);
        $this->assertItem($response['rewards']['history']['items']['0'], 5, 'Newsletter subscription', 5, 10);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_admin.php
     */
    public function testGetCustomerRewardsHistoryAdminItem()
    {
        $query = $this->getQueryHistory();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertEquals('1', $response['rewards']['history']['total_count']);
        $this->assertIsArray($response['rewards']['history']['items']);
        $this->assertNotEmpty($response['rewards']['history']['items']);
        $this->assertItem($response['rewards']['history']['items']['0'], 10, 'Admin Point Change', 10);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_newsletter.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_registration.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_admin.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_rule_registration.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_rule_newsletter.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/deduct_points.php
     */
    public function testGetCustomerRewardsHistoryItems()
    {
        $query = $this->getQueryHistory();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertEquals('4', $response['rewards']['history']['total_count']);
        $this->assertIsArray($response['rewards']['history']['items']);
        $this->assertNotEmpty($response['rewards']['history']['items']);
        $this->assertItem($response['rewards']['history']['items']['0'], -3.25, 'Admin Point Change', 26.75);
        $this->assertItem($response['rewards']['history']['items']['1'], 5, 'Newsletter subscription', 30, 10);
        $this->assertItem($response['rewards']['history']['items']['2'], 15, 'Registration', 25);
        $this->assertItem($response['rewards']['history']['items']['3'], 10, 'Admin Point Change', 10);
    }

    /**
     * @return string
     */
    private function getQueryHistory(): string
    {
        $query = <<<QUERY
{
    rewards {
        history {
            total_count
            items (pageSize:10, currentPage:1) {
                action_date
                amount
                action
                points_left
                expiration_date
            }
        }
    }
}
QUERY;
        return $query;
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
     * @param array $item
     * @param float $amount
     * @param string $action
     * @param float $points_left
     * @param int $exp_period
     *
     * @return void
     */
    private function assertItem(array $item, float $amount, string $action, float $pointsLeft, int $expPeriod = 0)
    {
        $dateHour = date("Y-m-d H");
        $expDate = date('Y-m-d', strtotime('+ ' . $expPeriod . ' days'));

        $this->assertStringContainsString($dateHour, $item['action_date']);
        $this->assertEquals($amount, $item['amount']);
        $this->assertEquals($action, $item['action']);
        $this->assertEquals($pointsLeft, $item['points_left']);
        if ($expPeriod == 0) {
            $this->assertNull($item['expiration_date']);
        } else {
            $this->assertStringContainsString($expDate, $item['expiration_date']);
        }
    }
}
