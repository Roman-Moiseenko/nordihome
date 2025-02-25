<?php
declare(strict_types=1);

namespace App\Modules\Base\Service;

class YandexTranslate
{
    public static function translate($text)
    {
        $IAM_TOKEN = env('IAM_TOKEN', null);
        $folder_id = env('IAM_FOLDER', null);
        $target_language = 'ru';
       // $texts = ["Hello", "World"];

        $url = 'https://translate.api.cloud.yandex.net/translate/v2/translate';

        $headers = [
            'Content-Type: application/json',
            "Authorization: Bearer $IAM_TOKEN"
        ];

        $post_data = [
            "targetLanguageCode" => $target_language,
            "texts" => $text,
            "folderId" => $folder_id,
        ];

        $data_json = json_encode($post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);

        $result = curl_exec($curl);

        curl_close($curl);

        var_dump($result);
    }
}
