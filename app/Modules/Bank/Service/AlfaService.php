<?php

namespace App\Modules\Bank\Service;

class AlfaService
{

    public function test()
    {
        $ch = curl_init();
        $cert = resource_path('cert') . '/baas_swagger_2025.p12';
        $url = 'https://sandbox.alfabank.ru/api/v1/endpoint';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json']);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_SSLCERT, $cert);
      //  curl_setopt($ch, CURLOPT_SSLKEY, 'client.key');
     //   curl_setopt($ch, CURLOPT_CAINFO, 'ca.crt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
    }
}
