<?php


namespace App\Modules\Base\Service;



use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Repository\SettingRepository;
use JetBrains\PhpStorm\Deprecated;

class HttpPage
{
    protected ?Cache $cache;
    protected array $parsdomains = [];
    private Parser $parser;

    public function __construct(Cache $cache = null)
    {
        $this->cache = $cache;

        $settings = new SettingRepository();
        $this->parser =  $settings->getParser();
    }

    public function addDomainToRequest(string $url)
    {
        $this->parsdomains[] = $url;
    }

    public function isUseCache(): bool
    {
        return !is_null($this->cache);
    }

    public function getPage(string $url, string $prefix = ''): ?string
    {
        $result = null;
        if ($this->isUseCache()) {
            $key = $prefix . md5($url) . '.html';
            $this->cache->isValid($key, 1000 * 60 * 24 * 5); //5 days
            $val = $this->cache->get($key);
            if ($val !== NULL) {
                $result = $val;
            } else {
                //$res = array("error" => null, "response" => FALSE);
                $res = $this->request($url);
                if ($res['http_code'] != '200') {
                    //var_dump($res);
                    return null;
                }
                if ($res['error'] === NULL && $res['http_code'] == '200') {
                    $result = $res["response"];
                    $this->cache->set($key, $result);
                }
            }
        } else {
            $res = $this->request($url);
            if ($res['error'] === NULL) {
                $result = $res["response"];
            }
        }

        return $result;
    }

    public function post(
        $url = '',
        $params = [],
        $json = '',
    )
    {
        $result = ["error" => null, "response" => FALSE, "http_code" => '200'];
        $headers = [
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            //"Accept-Encoding: gzip, deflate",
            "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
            "Cache-Control: max-age=0",
            "Connection: keep-alive",
            "Content-Type: application/json",
            "x-client-id: b6c117e5-ae61-4ef5-b4cc-e0b1e37f0631",
        //    'authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3MzczODc4MzQsImp0aSI6Ii8zdytaRm5rL09TNlJlQ2s0bG1URVNYVjZCWXM5Qk52Z2RUR3JQY0RsZ3c9IiwiaXNzIjoibG9jYWxob3N0IiwiZXhwIjoxNzM3Mzg5NjM0LCJkYXRhIjp7ImN1c3RvbWVyIjoiYTkyZjgzYmItZGE2Mi00NGU5LWFmZWEtMTA0Mzg1ZTQ4MzM3IiwiY3VzdG9tZXJHcm91cCI6bnVsbH19.CmRoum73sPNj0e54s5Iv-8a6l8CwTHVLqKgK8TQMmFSrzxoOauOpSRRzZmlRrYPfM_-hyaEfMklgGxhgLgbYzg',
         //   'Cookie: test-luigi=false; CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22ad_user_data%22%3A%22targeting%22%2C%22ad_personalization%22%3A%22targeting%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%2C%22firstpage%22%3A%22https%3A%2F%2Fnbsklep.pl%2F%22%2C%22bannershown%22%3A1%2C%22action%22%3A%22accept%22%2C%22categorie…ZGE2Mi00NGU5LWFmZWEtMTA0Mzg1ZTQ4MzM3IiwiY3VzdG9tZXJHcm91cCI6bnVsbH19.tICS8P8kyPeK1YWKwrl6-8xBVlqty_71Abu2Z6ph65qf1XVuuOyQ9sF7icgQl2IaSH99yqYbJMBu9aMNKnZMAA%22%2C%22expirationDate%22:%222025-01-20T18:53:25+01:00%22}; newbalance_cart=6db1266b-696d-4562-bbbc-8c6e64c6e573; _snrs_sa=ssuid:d3c86223-8a41-40da-9b06-ce5d965d7198&appear:1737303621&sessionVisits:13; _snrs_sb=ssuid:d3c86223-8a41-40da-9b06-ce5d965d7198&leaves:1737394755; _uetsid=6acf72c0d5da11efb734e55975d25a63; _uetvid=6acf8890d5da11efa6103b4dbbb166be',
        ];
        if ($curl = curl_init()) {
            $_url = '';
            if (count($this->parsdomains) > 0) {
                $_url = array_shift($this->parsdomains);
                $this->parsdomains[] = $_url;
            }
            try {
                curl_setopt($curl, CURLOPT_URL, $_url . $url);
                curl_setopt($curl, CURLOPT_POST, 1);
                if (!empty($params))
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params, JSON_UNESCAPED_UNICODE));
                if (!empty($json))
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl, CURLOPT_TIMEOUT, 60);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                if (!empty($this->parser->proxy_ip) && $this->parser->with_proxy) {
                    curl_setopt($curl, CURLOPT_PROXY, $this->parser->proxy_ip);
                    curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->parser->proxy_user);
                }

                $result["response"] = curl_exec($curl);
                // dd($result);
                if (!curl_errno($curl)) {
                    $info = curl_getinfo($curl);
                    $result["http_code"] = $info['http_code'];
                }

                curl_close($curl);
                if ($result["response"] === false) {
                    $result['error'] = 'CURL_FALSE_RESPONSE';
                } else {
                    //$result["response"] = $this->gzdecode($result["response"]);
                }
            } catch (\DomainException $E) {
                $result['error'] = $E->getMessage();
            }
        } else {
            $result['error'] = 'CURL_NO_INIT';
        }

        return $result;
    }

    protected function request($url): array
    {

        $result = ["error" => null, "response" => FALSE, "http_code" => '200'];
        $headers = [
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            //"Accept-Encoding: gzip, deflate",
            "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
            "Cache-Control: max-age=0",
            "Connection: keep-alive",
            "x-client-id: b6c117e5-ae61-4ef5-b4cc-e0b1e37f0631"
        ];
        if ($curl = curl_init()) {
            $_url = '';
            if (count($this->parsdomains) > 0) {
                $_url = array_shift($this->parsdomains);
                $this->parsdomains[] = $_url;
            }
            try {
                curl_setopt($curl, CURLOPT_URL, $_url . $url);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl, CURLOPT_TIMEOUT, 60);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                if (!empty($this->parser->proxy_ip) && $this->parser->with_proxy) {
                    curl_setopt($curl, CURLOPT_PROXY, $this->parser->proxy_ip);
                    curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->parser->proxy_user);
                }

                $result["response"] = curl_exec($curl);
               // dd($result);
                if (!curl_errno($curl)) {
                    $info = curl_getinfo($curl);
                    $result["http_code"] = $info['http_code'];
                }

                curl_close($curl);
                if ($result["response"] === false) {
                    $result['error'] = 'CURL_FALSE_RESPONSE';
                } else {
                    //$result["response"] = $this->gzdecode($result["response"]);
                }
            } catch (\DomainException $E) {
                $result['error'] = $E->getMessage();
            }
        } else {
            $result['error'] = 'CURL_NO_INIT';
        }

        return $result;
    }


    public function getHeaders(string $url): array
    {
        if ($this->isUseCache()) {
            $key = md5($url) . '.headers';
            $this->cache->isValid($key, 1000 * 60 * 24 * 5); //5 days
            $str = $this->cache->get($key);
            if ($str !== NULL) {
                $headers = unserialize($str);
            } else {
                $headers = @get_headers($url);
                $this->cache->set($key, serialize($headers));
            }
        } else {
            $headers = @get_headers($url);
        }
        return $headers;
    }

    /**
     * Загрузить содержимое файла через Прокси
     * @param $url
     * @return bool|string
     */
    #[Deprecated]
    public function dlFile($url): bool|string
    {
        $headers = [
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            //"Accept-Encoding: gzip, deflate",
            "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
            "Cache-Control: max-age=0",
            "Connection: keep-alive",
            "x-client-id: b6c117e5-ae61-4ef5-b4cc-e0b1e37f0631"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ($curl, CURLOPT_URL,$url);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, true);
        if (!empty($this->parser->proxy_ip)) {
            curl_setopt($curl, CURLOPT_PROXY, $this->parser->proxy_ip);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->parser->proxy_user);
        }

        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }
}
