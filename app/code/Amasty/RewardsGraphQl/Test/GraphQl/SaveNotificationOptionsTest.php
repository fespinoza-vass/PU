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

class SaveNotificationOptionsTest extends GraphQlAbstract
{
    public const REWARD_CUSTOMER = 'rewardspoints@amasty.com';
    public const REWARD_CUSTOMER_PASS = 'rewardspassword';
    public const MAIN_QUERY_KEY = 'saveNotificationOptions';

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
    public function testSaveNotificationOptions()
    {
        $vars = [
            'earnOption' => true,
            'expireOption' => false
        ];

        $query = $this->getQuery();
        $response = $this->graphQlMutation($query, $vars, '', $this->getHeader());

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertEquals('Notification options were changed.', $response[self::MAIN_QUERY_KEY]['response']);
    }

    /**
     * @return string
     */
    private function getQuery(): string
    {
        return <<<'MUTATION'
mutation SaveNotification(
    $earnOption: Boolean,
    $expireOption: Boolean
){
    saveNotificationOptions (
        input: {
            earn_option: $earnOption
            expire_option: $expireOption
        }
    ) {
        response
    }
}
MUTATION;
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
}
