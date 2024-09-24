<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Test\GraphQl;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class ApplyRewardsTest extends GraphQlAbstract
{
    public const REWARD_CUSTOMER = 'rewardspoints@amasty.com';
    public const REWARD_CUSTOMER_PASS = 'rewardspassword';

    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var QuoteIdToMaskedQuoteIdInterface
     */
    private $quoteIdToMaskedQuoteId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerTokenService = Bootstrap::getObjectManager()->get(CustomerTokenServiceInterface::class);
        $this->customerRepository = Bootstrap::getObjectManager()->get(CustomerRepositoryInterface::class);
        $this->quoteRepository = Bootstrap::getObjectManager()->get(QuoteRepository::class);
        $this->quoteIdToMaskedQuoteId = Bootstrap::getObjectManager()->get(QuoteIdToMaskedQuoteIdInterface::class);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_admin.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/points/rate 1
     */
    public function testApplyRewardPointsValid()
    {
        $usePoints = 5;

        $query = $this->getMutationUseRewardsQuery($usePoints);

        $response = $this->graphQlMutation($query, [], '', $this->getHeader());

        $this->assertStringContainsString(
            'You used <span id="am-used-points">' . $usePoints . '</span> point(s).',
            $response['useRewardPoints']['response']
        );
        $this->assertStringContainsString(self::REWARD_CUSTOMER, $response['useRewardPoints']['cart']['email']);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_admin.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/points/rate 1
     */
    public function testApplyRewardPointsTooMuch()
    {
        $usePoints = 11;

        $query = $this->getMutationUseRewardsQuery($usePoints);

        $response = $this->graphQlMutation($query, [], '', $this->getHeader());

        $this->assertStringContainsString("Too much point(s) used.", $response['useRewardPoints']['response']);
        $this->assertStringContainsString(self::REWARD_CUSTOMER, $response['useRewardPoints']['cart']['email']);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_admin.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/points/rate 1
     */
    public function testApplyRewardPointsRemoved()
    {
        $usePoints = 0;

        $query = $this->getMutationUseRewardsQuery($usePoints);

        $response = $this->graphQlMutation($query, [], '', $this->getHeader());

        $this->assertStringContainsString("Removed.", $response['useRewardPoints']['response']);
        $this->assertStringContainsString(self::REWARD_CUSTOMER, $response['useRewardPoints']['cart']['email']);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_points_by_admin.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/points/rate 1
     */
    public function testApplyRewardPointsInvalid()
    {
        $usePoints = -1;

        $query = $this->getMutationUseRewardsQuery($usePoints);

        $response = $this->graphQlMutation($query, [], '', $this->getHeader());

        $this->assertStringContainsString(
            "Points \"$usePoints\" not valid.",
            $response['useRewardPoints']['response']
        );
        $this->assertStringContainsString(self::REWARD_CUSTOMER, $response['useRewardPoints']['cart']['email']);
    }

    /**
     * @param float $points_amount
     *
     * @return string
     */
    private function getMutationUseRewardsQuery(float $pointsAmount): string
    {
        $customerId = $this->customerRepository->get(self::REWARD_CUSTOMER)->getId();
        $quote = (int)$this->quoteRepository->getActiveForCustomer($customerId)->getId();
        $maskedId = $this->quoteIdToMaskedQuoteId->execute($quote);

        return <<<QUERY
mutation {
  useRewardPoints(
        input: {
             cart_id: "$maskedId",
             points: "$pointsAmount"
        }
  ) {
      response
      cart {
          email
      }
  }
}
QUERY;
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
}
