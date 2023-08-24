<?php
namespace WolfSellers\Checkout\Plugin\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class DefaultConfigProvider
{
    /** @var ScopeConfigInterface */
    private ScopeConfigInterface $scopeConfig;

    /** @var StoreManagerInterface */
    private StoreManagerInterface $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Return admin configuration.
     *
     * @param $path
     * @return mixed
     */
    public function getConfigData($path): mixed
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * After get plugin, for add var to checkout config javascript var.
     *
     * @param DefaultConfigProvider $subject
     * @param array $result
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetConfig($subject, array $result)
    {
        $mediaUrl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $result["benefitsModal"] = [
            'enable' => $this->getConfigData('onepage/benefits_modal/enable') == "1",
            'image' => $mediaUrl . 'theme_customization/' . $this->getConfigData('onepage/benefits_modal/image')
        ];
        return $result;
    }
}
