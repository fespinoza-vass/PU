<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vass\NiubizVisanet\Model;

use Magento\Store\Model\ScopeInterface;

class Config
{
    private function getValueConfig($field, $storeId = null)
    {
      
        $pathPattern = 'payment/%s/vassvisanet/%s';
        $methodCode = 'vassvisanet';

        return $this->scopeConfig->getValue(
            sprintf($pathPattern, $methodCode, $field),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function configurationNiubiz()
    {
        return $this->getValueConfig('merchant_id')
        && $this->getValueConfig('public_key')
        && $this->getValueConfig('private_key')
        && $this->getValueConfig('debug')
        && $this->getValueConfig('ip_client');
    }

    
    public function authorization($environment,$key,$amount,$transactionToken,$purchaseNumber,$merchantId,$currencyCode){
        
        $url = match ($environment) {
            'prd' => "https://apiprod.vnforapps.com/api.authorization/v3/authorization/ecommerce/".$merchantId,
            'dev' => "https://apitestenv.vnforapps.com/api.authorization/v3/authorization/ecommerce/".$merchantId,
            default => throw new \InvalidArgumentException("Ambiente desconocido: $environment"),
        };

        $header = array("Content-Type: application/json","Authorization: $key");
        $request_body="{

        \"antifraud\" : null,
        \"captureType\" : \"manual\",
        \"channel\" : \"web\",
        \"countable\" : true,
        \"order\" : {
                \"amount\" : \"$amount\",
                \"tokenId\" : \"$transactionToken\",
                \"purchaseNumber\" : \"$purchaseNumber\",
                \"currency\" : \"$currencyCode\"
            }
        }";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        #curl_setopt($ch, CURLOPT_USERPWD, "$accessKey:$secretKey");
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);        
        $json = json_decode($response, TRUE);
        $json["statusCode"]=$status;    
        $json = json_encode($json, JSON_PRETTY_PRINT);        
        return $json;
    }

    public function securitykey($environment,$merchantId,$user,$password){
        $url = match ($environment) {
            'prd' => "https://apiprod.vnforapps.com/api.security/v1/security",
            'dev' => "https://apitestenv.vnforapps.com/api.security/v1/security",
            default => throw new \InvalidArgumentException("Ambiente desconocido: $environment"),
        };
        
        $accessKey = $user;
        $secretKey = $password;
        $header = array("Content-Type: application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$accessKey:$secretKey");
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        #curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        #curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $key = curl_exec($ch);
        return $key;
    }

    public function create_token($environment,$amount,$key,$merchantId,$user,$password,$ipClient){
        $url = match ($environment) {
            'prd' => "https://apiprod.vnforapps.com/api.ecommerce/v2/ecommerce/token/session/".$merchantId,
            'dev' => "https://apitestenv.vnforapps.com/api.ecommerce/v2/ecommerce/token/session/".$merchantId,
            default => throw new \InvalidArgumentException("Ambiente desconocido: $environment"),
        };
        
        $accessKey = $user;
        $secretKey = $password;

        $header = array("Content-Type: application/json","Authorization: $key");
        //var_dump($header);
        //$ip = $_SERVER['HTTP_CLIENT_IP'];
        $request_body="{
            \"amount\" : {$amount},
            \"channel\" : \"web\",
            \"antifraud\" : {
                \"clientIp\" : \"{$ipClient}\",
                \"merchantDefineData\" : {
                    \"MDD1\" : \"web\",
                    \"MDD2\" : \"Canl\",
                    \"MDD3\" : \"Canl\"
                }
            }
        }";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "$accessKey:$secretKey");
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        #var_dump($response);
        $json = json_decode($response);
        $dato = $json->sessionKey;
        return $dato;
    }

}
