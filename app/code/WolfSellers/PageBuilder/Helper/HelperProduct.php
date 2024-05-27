<?php

namespace WolfSellers\PageBuilder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class HelperProduct extends AbstractHelper
{
    protected ProductRepository $productRepository;

    /**
     * @param ProductRepository $productRepository
     * @param Context $context
     */
    public function __construct(
        ProductRepository $productRepository,
        Context $context
    ) {
        $this->productRepository = $productRepository;

        parent::__construct($context);
    }


    /**
     * @param $id
     * @param $attribute
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getAttrValue($id, $attribute)
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute($attribute) ? $product->getCustomAttribute($attribute)->getValue() : '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDarkMax($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('dark_circle_score_max') ?
            'data-darkCircles-max="' . $product->getCustomAttribute('dark_circle_score_max')->getValue() . '"' :
            '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDarkMin($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('dark_circle_score_min') ?
            'data-darkCircles-min="' . $product->getCustomAttribute('dark_circle_score_min')->getValue() . '"' :
            '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSpotkMax($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('spot_score_max') ?
            'data-ageSpots-max="' . $product->getCustomAttribute('spot_score_max')->getValue() . '"' :
            '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSpotMin($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('spot_score_min') ?
            'data-ageSpots-min="' . $product->getCustomAttribute('spot_score_min')->getValue() . '"' :
            '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getWrinkleMax($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('wrinkle_score_max') ?
            'data-wrinkles-max="' . $product->getCustomAttribute('wrinkle_score_max')->getValue() . '"' :
            '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getWrinkleMin($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('wrinkle_score_min') ?
            'data-wrinkles-min="' . $product->getCustomAttribute('wrinkle_score_min')->getValue() . '"' :
            '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTextureMax($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('texture_score_max') ?
            'data-texture-max="'.$product->getCustomAttribute('texture_score_max')->getValue() . '"' :
            '';
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTextureMin($id): string
    {
        $product = $this->productRepository->getById($id);

        return $product->getCustomAttribute('texture_score_min') ?
            'data-texture-min="'.$product->getCustomAttribute('texture_score_min')->getValue() . '"' :
            '';
    }

}

