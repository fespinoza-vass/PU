<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-26
 * Time: 17:47
 */

declare(strict_types=1);

namespace WolfSellers\OrderTracking\Controller\Status;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Shipping\Model\Order\Track;
use Magento\Shipping\Model\ResourceModel\Order\Track\CollectionFactory as TrackCollectionFactory;
use Magento\Shipping\Model\Tracking\Result\Status;
use WolfSellers\OrderTracking\Block\TrackingStatus;

/**
 * Status Controller.
 */
class Index implements HttpPostActionInterface
{
    /** @var Http */
    private RequestInterface $request;

    /** @var JsonFactory  */
    private JsonFactory $jsonFactory;

    /** @var TrackCollectionFactory */
    private TrackCollectionFactory $trackCollectionFactory;

    /** @var PageFactory */
    private PageFactory $pageFactory;

    /** @var CarrierFactory */
    private CarrierFactory $carrierFactory;

    /**
     * @param RequestInterface $request
     * @param JsonFactory $jsonFactory
     * @param TrackCollectionFactory $trackCollectionFactory
     * @param PageFactory $pageFactory
     * @param CarrierFactory $carrierFactory
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory,
        TrackCollectionFactory $trackCollectionFactory,
        PageFactory $pageFactory,
        CarrierFactory $carrierFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->trackCollectionFactory = $trackCollectionFactory;
        $this->pageFactory = $pageFactory;
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $trackingNumber = trim($this->request->getPostValue('tracking_number', ''));
        $track = $this->getShipmentTrackByTrackNumber($trackingNumber);

        if (!$track) {
            return $resultJson->setData([
                'success' => false,
                'message' => __('Tracking number not found.'),
            ]);
        }

        $trackingInfo = $track->getNumberDetail();

        if (!$trackingInfo instanceof Status || empty($trackingInfo->getProgressdetail())) {
            return $resultJson->setData([
                'success' => false,
                'message' => __('Tracking info not available.'),
            ]);
        }

        $response = [
            'success' => true,
            'html' => $this->getTrackingInfoHtml($track, $trackingInfo),
        ];

        return $resultJson->setData($response);
    }

    /**
     * Get shipment tracking by track number.
     *
     * @param string $trackNumber
     *
     * @return Track|null
     */
    private function getShipmentTrackByTrackNumber(string $trackNumber): ?Track
    {
        $collection = $this->trackCollectionFactory->create();
        $collection->addFieldToFilter(ShipmentTrackInterface::TRACK_NUMBER, $trackNumber);

        return $collection->count() > 0 ? $collection->getFirstItem() : null;
    }

    /**
     * Tracking info html.
     *
     * @param Track $track
     * @param Status $trackingInfo
     *
     * @return string
     */
    private function getTrackingInfoHtml(Track $track, Status $trackingInfo): string
    {
        $page = $this->pageFactory->create();

        /** @var TrackingStatus $block */
        $block = $page->getLayout()->createBlock(TrackingStatus::class);
        $block->setShipmentTrack($track);
        $block->setTrackingInfo($trackingInfo);

        return $block->toHtml();
    }
}
