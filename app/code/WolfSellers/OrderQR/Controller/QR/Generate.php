<?php

namespace WolfSellers\OrderQR\Controller\QR;

use Magento\Framework\App\Action\Context;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Generate extends \Magento\Framework\App\Action\Action
{

    public function __construct(

        Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $writer->writeFile('http://perfumerias.test/', 'qrcode.png');

        echo 'Hello World';
        exit();
    }
}
