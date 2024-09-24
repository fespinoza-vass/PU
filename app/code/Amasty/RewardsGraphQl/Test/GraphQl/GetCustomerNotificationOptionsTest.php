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

class GetCustomerNotificationOptionsTest extends GraphQlAbstract
{
    public const REWARD_CUSTOMER = 'rewardspoints@amasty.com';
    public const REWARD_CUSTOMER_PASS = 'rewardspassword';
    public const MAIN_QUERY_KEY = 'getCustomerNotificationOptions';

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
     * @magentoConfigFixture base_website amrewards/notification/send_earn_notification 1
     * @magentoConfigFixture base_website amrewards/notification/subscribe_by_default_to_earn_notifications 0
     * @magentoConfigFixture base_website amrewards/notification/send_expire_notification 1
     * @magentoConfigFixture base_website amrewards/notification/subscribe_by_default_to_expire_notifications 0
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     */
    public function testGetCustomerNotificationOptions()
    {
        $fields = [
            'amrewards_earning_notification' => false,
            'amrewards_expire_notification' => true
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
    getCustomerNotificationOptions {
        amrewards_earning_notification
        amrewards_expire_notification
    }
}
QUERY;
        return $query;
    }
}
