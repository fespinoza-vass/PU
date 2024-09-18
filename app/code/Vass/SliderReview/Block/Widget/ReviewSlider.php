<?php

namespace Vass\SliderReview\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class ReviewSlider extends Template implements BlockInterface
{
    /**
     * Template file for the widget
     *
     * @var string
     */
    protected $_template = 'widget/review_slider.phtml';

    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManager $storeManager
     */
    public function __construct(
        Context $context,
        protected StoreManager $storeManager
    ) {
        parent::__construct($context);
    }

    /**
     * Get the slider images
     *
     * @return array
     */
    public function getImages()
    {
        $image1 = $this->getData('image_1');
        $image2 = $this->getData('image_2');
        $image3 = $this->getData('image_3');
        $image4 = $this->getData('image_4');
        $image5 = $this->getData('image_5');
        $image6 = $this->getData('image_6');
        $image7 = $this->getData('image_7');
        $image8 = $this->getData('image_8');
        return [$image1, $image2, $image3, $image4, $image5, $image6, $image7, $image8];
    }

    /**
     * Get the slider texts
     *
     * @return array
     */
    public function getText()
    {
        $imageText1 = $this->getData('text_1');
        $imageText2 = $this->getData('text_2');
        $imageText3 = $this->getData('text_3');
        $imageText4 = $this->getData('text_4');
        $imageText5 = $this->getData('text_5');
        $imageText6 = $this->getData('text_6');
        $imageText7 = $this->getData('text_7');
        $imageText8 = $this->getData('text_8');
        return [$imageText1, $imageText2, $imageText3, $imageText4, $imageText5, $imageText6, $imageText7, $imageText8];
    }

    /**
     * Get the slider titles
     *
     * @return string
     */
    public function getTitle()
    {
        $title = $this->getData('title');

        return $title;
    }

    /**
     * Get the slider labels
     *
     * @return array
     */
    public function getLabel() : array
    {
        $imageLabel1 = $this->getData('label_1');
        $imageLabel2 = $this->getData('label_2');
        $imageLabel3 = $this->getData('label_3');
        $imageLabel4 = $this->getData('label_4');
        $imageLabel5 = $this->getData('label_5');
        $imageLabel6 = $this->getData('label_6');
        $imageLabel7 = $this->getData('label_7');
        $imageLabel8 = $this->getData('label_8');
        return [$imageLabel1,
            $imageLabel2,
            $imageLabel3,
            $imageLabel4,
            $imageLabel5,
            $imageLabel6,
            $imageLabel7,
            $imageLabel8
        ];
    }

    /**
     * Get the slider URLs
     *
     * @return array
     */
    public function getNames() : array
    {
        $imageName1 = $this->getData('name_1');
        $imageName2 = $this->getData('name_2');
        $imageName3 = $this->getData('name_3');
        $imageName4 = $this->getData('name_4');
        $imageName5 = $this->getData('name_5');
        $imageName6 = $this->getData('name_6');
        $imageName7 = $this->getData('name_7');
        $imageName8 = $this->getData('name_8');
        return [$imageName1, $imageName2, $imageName3, $imageName4, $imageName5, $imageName6, $imageName7, $imageName8];
    }

    /**
     * Get the slider stars
     *
     * @return array
     */
    public function getStars() : array
    {
        $imageStar1 = $this->getData('star_1');
        $imageStar2 = $this->getData('star_2');
        $imageStar3 = $this->getData('star_3');
        $imageStar4 = $this->getData('star_4');
        $imageStar5 = $this->getData('star_5');
        $imageStar6 = $this->getData('star_6');
        $imageStar7 = $this->getData('star_7');
        $imageStar8 = $this->getData('star_8');
        return [$imageStar1, $imageStar2, $imageStar3, $imageStar4, $imageStar5, $imageStar6, $imageStar7, $imageStar8];
    }
}
