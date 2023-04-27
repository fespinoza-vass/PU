<?php

namespace PechoSolutions\Visanet\Model\Library;

class Visanet
{

    public function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12).$hyphen
                .chr(125);// "}"
            $uuid = substr($uuid, 1, 36);
            return $uuid;
        }
    }
    public function create_json_post($post){
        $request="{";
        for ($i=0; $i < count($post) ; $i++) {
            $llave = key($post);
            $valor = $post[$llave];
            if($i==count($post)-1){
                $request = $request. "\"$llave\":\"$valor\"";
            }else{
                $request = $request. "\"$llave\":\"$valor\",";
            }
            next($post);
        }
        $request = $request."}";
        return $request;
    }

    public function contador(){
        $archivo = "contador.txt";
        $contador = 0;
        $fp = fopen($archivo,"r");
        $contador = fgets($fp, 26);
        fclose($fp);
        ++$contador;
        $fp = fopen($archivo,"w+");
        fwrite($fp, $contador, 26);
        fclose($fp);
        return $contador;
    }
    public function guarda_sessionToken($sessionToken){
        $archivo = "sessionToken.txt";
        $fp = fopen($archivo,"w+");
        fwrite($fp, $sessionToken, 96);
        fclose($fp);
    }
    public function recupera_sessionToken(){
        $archivo = "sessionToken.txt";
        $fp = fopen($archivo,"r");
        $valor = fgets($fp, 96);
        fclose($fp);
        return $valor;
    }

    public function guarda_sessionKey($sessionKey){
        $archivo = "sessionKey.txt";
        $fp = fopen($archivo,"w+");
        fwrite($fp, $sessionKey, 96);
        fclose($fp);
    }

    public function recupera_sessionKey(){
        $archivo = "sessionKey.txt";
        $fp = fopen($archivo,"r");
        $valor = fgets($fp, 96);
        fclose($fp);
        return $valor;
    }

    public function authorization($environment,$key,$amount,$transactionToken,$purchaseNumber,$merchantId,$currencyCode){
        switch ($environment) {
            case 'prd':
                #$merchantId = merchantIdprd;
                $url = "https://apiprod.vnforapps.com/api.authorization/v3/authorization/ecommerce/".$merchantId;
                break;
            case 'dev':
                #$merchantId = merchantidtest;
                $url = "https://apitestenv.vnforapps.com/api.authorization/v3/authorization/ecommerce/".$merchantId;
                break;
        }
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


    public function post_form($array_post,$url){
        $html="<html>
    <head>
    </head>
    <Body onload=\"f1.submit();\">
    <form name=\"f1\" method=\"post\" action=\"{$url}\">";
        for ($i=0; $i < count($array_post) ; $i++) {
            $llave = key($array_post);
            $valor = $array_post[$llave];
            $html = $html."<input type=\"hidden\" name=\"$llave\" value=\"$valor\" />";
            next($array_post);
        }
        $html = $html."</form>
    </body>
    </html>";
        return $html;
    }


    public function jsonpp($json, $istr='  ')
    {
        $result = '';
        for($p=$q=$i=0; isset($json[$p]); $p++)
        {
            $json[$p] == '"' && ($p>0?$json[$p-1]:'') != '\\' && $q=!$q;
            if(strchr('}]', $json[$p]) && !$q && $i--)
            {
                strchr('{[', $json[$p-1]) || $result .= "\n".str_repeat($istr, $i);
            }
            $result .= $json[$p];
            if(strchr(',{[', $json[$p]) && !$q)
            {
                $i += strchr('{[', $json[$p])===FALSE?0:1;
                strchr('}]', $json[$p+1]) || $result .= "\n".str_repeat($istr, $i);
            }
        }
        return $result;
    }

    public function securitykey($environment,$merchantId,$user,$password){
        switch ($environment) {
            case 'prd':
                //$merchantId = merchantidprd;
                //$url = securityapiprd;
                $url = "https://apiprod.vnforapps.com/api.security/v1/security";
                $accessKey=$user;
                $secretKey=$password;
                break;
            case 'dev':
                //$merchantId = merchantidtest;
                $url = "https://apitestenv.vnforapps.com/api.security/v1/security";
                $accessKey=$user;
                $secretKey=$password;
                break;
        } 
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
        switch ($environment) {
            case 'prd':
                #$merchantId = merchantIdprd;
                $url = "https://apiprod.vnforapps.com/api.ecommerce/v2/ecommerce/token/session/".$merchantId;
                $accessKey=$user;
                $secretKey=$password;
                break;
            case 'dev':
                #$merchantId = merchantidtest;
                $url = "https://apitestenv.vnforapps.com/api.ecommerce/v2/ecommerce/token/session/".$merchantId;
                $accessKey=$user;
                $secretKey=$password;
                break;
        }
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