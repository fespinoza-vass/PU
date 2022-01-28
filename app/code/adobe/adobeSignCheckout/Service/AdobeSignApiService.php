<?php

namespace adobe\adobeSignCheckout\Service;

use Dompdf\Dompdf;

class AdobeSignApiService
{
    protected $logger;
    protected $curlClient;
    protected $helperData;
    protected $templateFactory;
    protected $templateResource;
    protected $checkoutSession;
    protected $filesystem;
    protected $objectManager;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curlClient,
        \adobe\adobeSignCheckout\Helper\Data $helperData,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Email\Model\TemplateFactory $template,
        \Magento\Email\Model\ResourceModel\Template $templateResource,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helperData = $helperData;
        $this->curlClient = $curlClient;
        $this->logger = $logger;
        $this->templateFactory = $template;
        $this->templateResource = $templateResource;
        $this->checkoutSession = $checkoutSession;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->objectManager = $objectManager;
    }

    public function isSignAgreementRequired($quote)
    {
        // check store group
        $storeGroupId = $this->storeManager->getStore()->getStoreGroupId();
        $validStoreCodes = $this->helperData->getShops();
        if (!in_array($storeGroupId, $validStoreCodes)) {
            return false;
        }

        // check item category
        $items = $quote->getAllItems();
        $prodCats = $this->helperData->getProdCategories();

        $categoryFactory = $this->objectManager->get('\Magento\Catalog\Model\CategoryFactory');
        $childCatIds = array();
        foreach ($prodCats as $id) {
            $category = $categoryFactory->create()->load($id);
            $childCatIds = array_merge($childCatIds, explode(",", $category->getChildren(true)));
        }
        $prodCats = array_merge($prodCats, $childCatIds);

        foreach ($items as $item) {
            $productid = $item->getProductId();
            $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load($productid);
            $categoriesIds = $product->getCategoryIds();
            if (count(array_intersect($categoriesIds, $prodCats)) > 0) {
                return true;
            }
        }
        return false;
    }

    public function createAgreement($quote)
    {
        $agreementId = "";
        // generate the pdf
        $templateId = $this->helperData->getEmailTemplate();
        $this->logger->debug('template id :' . $templateId);
        $filePath = $this->genPdfFromEmailTemplate($templateId, $quote);
        $this->logger->debug('pdf file :' . $filePath);
        if (empty($filePath)) {
            $this->logger->warning('Failed to generate pdf file for agreement sign');
            return $agreementId;
        }

        $accessToken = $this->getAccessToken();
        //$this->logger->debug('access token :' . $accessToken);

        // upload the document
        $tranDocId = $this->createTransientId($accessToken, $filePath);
        //$this->logger->debug('transientDocumentId : ' . $tranDocId);
        if (empty($tranDocId)) {
            $this->logger->warning('Failed to upload pdf file to Adobe Sign');
            $this->removePdfFile($filePath);
            return $agreementId;
        }

        // create an agreement
        $agreementId = $this->createAgreementId($tranDocId, $quote, $accessToken);
        //$this->logger->debug('agreement id :' . $agreementId);
        if (empty($agreementId)) {
            $this->logger->warning('Failed to create agreement id');
        }
        $this->removePdfFile($filePath);
        return $agreementId;
    }

    private function loadTemplate($templateId)
    {
        $template = $this->templateFactory->create();
        $this->templateResource->load($template, $templateId);

        return $template;
    }

    private function genPdfFromEmailTemplate($templateId, $quote)
    {
        $testTemplate = $this->loadTemplate($templateId);

        $quoteData = $quote->getData();
        $items = [];
        foreach ($quote->getItems() as $item) {
            array_push($items, $item->getQty().' x '.$item->getName());
        }
        $quoteData['items'] = implode(' , ', $items);
        $addressData = $quote->getShippingAddress()->getData();
        $testTemplate->setVars(['quote' => $quoteData, 'address' => $addressData]);
        $html = $testTemplate->processTemplate();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html); //$html is html of template
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $basePath  =  $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)->getAbsolutePath();
        $fileName = $basePath . 'agreementTemplate_' . $quote->getId() . '_' . time() . '.pdf';
        file_put_contents($fileName, $dompdf->output());
        return $fileName;
    }

    private function removePdfFile($filePath)
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    private function getCurlClient()
    {
        return $this->curlClient;
    }

    public function getAccessToken()
    {
        $apiUrl = $this->helperData->getApiAccessPoint() . '/oauth/refresh';
        $params = [
                'refresh_token' => $this->helperData->getRefreshToken(),
                'client_id' => $this->helperData->getApplicationID(),
                'client_secret' => $this->helperData->getClientSecret(),
                'grant_type' => 'refresh_token'
        ];
        $headers = ["Content-Type" => "application/x-www-form-urlencoded"];

        try {
            $this->getCurlClient()->setHeaders($headers);
            $this->getCurlClient()->post($apiUrl, $params);
            $response = json_decode($this->getCurlClient()->getBody(), true);
            $status = $this->getCurlClient()->getStatus();
            //$this->logger->debug('get access token status :' . $status);
            if ($status != 200) {
                return "";
            } else {
                return $response['access_token'];
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
            return "";
        }
    }

    private function createTransientId($refreshToken, $filePath)
    {
        $apiUrl = $this->helperData->getApiAccessPoint() . '/api/rest/v6/transientDocuments';
        $fileBaseName = basename($filePath);
        $handle = curl_init();
        $header = [];
        $header[] = 'Content-Type: multipart/form-data';
        $header[] = 'Authorization: Bearer ' . $refreshToken;
        $header[] = 'Content-Disposition: form-data; name=";File"; filename="' . $fileBaseName . '"';
        //$this->logger->debug('header :' . json_encode($header));

        $fp = fopen($filePath, 'r');
        $contents = fread($fp, filesize($filePath));
        $postData = [
                "File" => $contents,
                "File-Name" => $fileBaseName,
                "Mime-Type" => 'application/pdf'
        ];
        curl_setopt_array(
            $handle,
            [CURLOPT_URL => $apiUrl,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => $postData,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => $header]
        );
        $response = curl_exec($handle);
        if (curl_errno($handle)) {
            $error_msg = curl_error($handle);
        }
        curl_close($handle);
        fclose($fp);
        if (isset($error_msg)) {
            $this->logger->error('Failed to upload agreement :' . $error_msg);
            return "";
        } else {
            $json = json_decode($response, true);
            return $json['transientDocumentId'];
        }
    }

    private function createAgreementId($tranDocId, $quote, $refreshToken)
    {
        $apiUrl = $this->helperData->getApiAccessPoint() . '/api/rest/v6/agreements';
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $refreshToken;

        $params = json_decode($this->helperData->getJson(), true);
        $params['participantSetsInfo'][0]['memberInfos'][0]['email'] = $quote->getCustomerEmail();
        $params['ExternalId'] = array("id" => $quote->getId());
        $params['fileInfos'] = [array('transientDocumentId' => $tranDocId)];

        //$this->logger->debug('post json :' . json_encode($params));
        $handle = curl_init();
        curl_setopt_array(
            $handle,
            [CURLOPT_URL => $apiUrl,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => json_encode($params),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => $headers]
        );

        $response = curl_exec($handle);
        if (curl_errno($handle)) {
            $error_msg = curl_error($handle);
        }
        curl_close($handle);
        if (isset($error_msg)) {
            $this->logger->error('Failed to create agreement :' . $error_msg);
        } else {
            $json = json_decode($response, true);
            return $json['id'];
        }
    }

    public function getSigningUrl($id)
    {
        $reqUrl = $this->helperData->getApiAccessPoint() . "/api/rest/v6/agreements/" . $id . "/signingUrls";
        //$this->logger->debug('get sign url: ' . $reqUrl);

        try {
            $accessToken =  $this->getAccessToken();
            $headers = ["Authorization" => 'Bearer ' . $accessToken];
            $this->getCurlClient()->setHeaders($headers);
            $this->getCurlClient()->get($reqUrl);
            $response = json_decode($this->getCurlClient()->getBody(), true);
            //$this->logger->debug('body :' . $this->getCurlClient()->getBody());
            $status = $this->getCurlClient()->getStatus();
            //$this->logger->debug('get sign url status :' . $status);
            if ($status === 200) {
                $signingUrlSetInfos = $response['signingUrlSetInfos'];
                $signingUrls = $signingUrlSetInfos[0]['signingUrls'];
                return $signingUrls[0]['esignUrl'];
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
            return "";
        }
        return "";
    }

    public function getAgreementStatusUrl()
    {
        return $this->helperData->getApiAccessPoint() . "/api/rest/v6/agreements";
    }

    public function getAgreementSignStatus($id)
    {
        $reqUrl = $this->getAgreementStatusUrl() . '/' . $id;
        //$this->logger->debug('check agreement url: ' . $reqUrl);

        try {
            $accessToken =  $this->getAccessToken();
            $headers = ["Authorization" => 'Bearer ' . $accessToken];
            $this->getCurlClient()->setHeaders($headers);
            $this->getCurlClient()->get($reqUrl);
            $response = json_decode($this->getCurlClient()->getBody(), true);
            //$this->logger->debug('body :' . $this->getCurlClient()->getBody());
            $status = $this->getCurlClient()->getStatus();
            //$this->logger->debug('get sign url status :' . $status);
            if ($status === 200) {
                $signStatus =  $response['status'];
                //$this->logger->debug('sign status :' . $signStatus);
                if ($signStatus === 'SIGNED') {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
            return "";
        }
        return "";
    }
}
