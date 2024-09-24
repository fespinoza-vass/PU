<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Test\GraphQl;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class GetRewardsHighlightTest extends GraphQlAbstract
{
    public const REWARD_CUSTOMER = 'rewardspoints@amasty.com';
    public const REWARD_CUSTOMER_PASS = 'rewardspassword';
    public const PROD_SKU = 'rew333SimProd';

    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerTokenService = Bootstrap::getObjectManager()->get(CustomerTokenServiceInterface::class);
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_order_complete.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoConfigFixture default_store amrewards/highlight/product 1
     * @magentoConfigFixture default_store amrewards/highlight/category 1
     * @magentoConfigFixture default_store amrewards/highlight/color #2577cf
     */
    public function testGetRewardsHighlightCategoryOrderRule()
    {
        $productId = $this->productRepository->get(self::PROD_SKU)->getId();
        $queryCategory = $this->getQueryHighlightCategory((string)$productId);

        $responseCategory = $this->graphQlQuery($queryCategory, [], '', $this->getHeader());

        $this->assertHighlight($responseCategory['rewards']['highlight']['category'], false, '#2577cf', '0');
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_order_complete.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_every_spent_highlight.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoConfigFixture default_store amrewards/highlight/product 1
     * @magentoConfigFixture default_store amrewards/highlight/category 1
     * @magentoConfigFixture default_store amrewards/highlight/color #4b11b8
     */
    public function testGetRewardsHighlightCategorySpentRule()
    {
        $productId = $this->productRepository->get(self::PROD_SKU)->getId();
        $queryCategory = $this->getQueryHighlightCategory((string)$productId);

        $responseCategory = $this->graphQlQuery($queryCategory, [], '', $this->getHeader());

        $this->assertHighlight($responseCategory['rewards']['highlight']['category'], true, '#4b11b8', '10');
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_every_spent_highlight.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoConfigFixture default_store amrewards/highlight/product 1
     * @magentoConfigFixture default_store amrewards/highlight/category 1
     * @magentoConfigFixture default_store amrewards/highlight/color #0e7d15
     */
    public function testGetRewardsHighlightCategoryTwoRules()
    {
        $productId = $this->productRepository->get(self::PROD_SKU)->getId();
        $queryCategory = $this->getQueryHighlightCategory((string)$productId);

        $responseCategory = $this->graphQlQuery($queryCategory, [], '', $this->getHeader());

        $this->assertHighlight($responseCategory['rewards']['highlight']['category'], true, '#0e7d15', '10');
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoConfigFixture default_store amrewards/highlight/product 1
     * @magentoConfigFixture default_store amrewards/highlight/category 1
     * @magentoConfigFixture default_store amrewards/highlight/color #ffffff
     */
    public function testGetRewardsHighlightCategoryWithoutRules()
    {
        $productId = $this->productRepository->get(self::PROD_SKU)->getId();
        $queryCategory = $this->getQueryHighlightCategory((string)$productId);

        $responseCategory = $this->graphQlQuery($queryCategory, [], '', $this->getHeader());

        $this->assertHighlight($responseCategory['rewards']['highlight']['category'], false, '#ffffff', '0');
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_order_complete.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/highlight/cart 1
     * @magentoConfigFixture default_store amrewards/highlight/color #b56f24
     */
    public function testGetRewardsHighlightCartOrderRule()
    {
        $query = $this->getQueryHighlightCart();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertHighlight($response['rewards']['highlight']['cart'], true, '#b56f24', '10');
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_every_spent_highlight.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/highlight/cart 1
     * @magentoConfigFixture default_store amrewards/highlight/color #49118a
     */
    public function testGetRewardsHighlightCartSpentRule()
    {
        $query = $this->getQueryHighlightCart();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertHighlight($response['rewards']['highlight']['cart'], true, '#49118a', '10');
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_order_complete.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/rules/rewards_rule_every_spent_highlight.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/highlight/cart 1
     * @magentoConfigFixture default_store amrewards/highlight/color #000000
     */
    public function testGetRewardsHighlightCartTwoRules()
    {
        $query = $this->getQueryHighlightCart();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertHighlight($response['rewards']['highlight']['cart'], true, '#000000', '20');
    }

    /**
     * @group amasty_rewards
     *
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/rewards_customer.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/category_product.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Amasty_RewardsGraphQl::Test/GraphQl/_files/customer/add_product_to_cart.php
     * @magentoConfigFixture default_store amrewards/highlight/cart 1
     * @magentoConfigFixture default_store amrewards/highlight/color #000000
     */
    public function testGetRewardsHighlightCartWithoutRules()
    {
        $query = $this->getQueryHighlightCart();

        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertHighlight($response['rewards']['highlight']['cart'], false, '#000000', '0');
    }

    /**
     * @param array $response
     * @param bool $visible
     * @param string $color
     * @param string $text
     *
     * @return void
     */
    public function assertHighlight(array $response, bool $visible, string $color, string $text)
    {
        if ($visible) {
            $this->assertTrue($response['visible']);
        } else {
            $this->assertFalse($response['visible']);
        }
        $this->assertEquals($color, $response['caption_color']);
        $this->assertEquals($text, $response['caption_text']);
    }

    /**
     * @param string $product_id
     *
     * @return string
     */
    private function getQueryHighlightCategory(string $productId): string
    {
        $query = <<<QUERY
{
    rewards {
        highlight {
            category (productId:"$productId", attributes:"") {
                visible
                caption_color
                caption_text
            }
        }
    }
}
QUERY;
        return $query;
    }

    /**
     * @return string
     */
    private function getQueryHighlightCart(): string
    {
        $query = <<<QUERY
{
    rewards {
        highlight {
            cart {
                visible
                caption_color
                caption_text
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
}
