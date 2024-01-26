<?php


namespace App\Modules\Shop\Parser;


class HttpPage
{
    protected ?Cache $cache;
    protected array $parsdomains = [];

    public function __construct(Cache $cache = null)
    {
        $this->cache = $cache;
    }

    public function addDomainToRequest(string $url)
    {
        $this->parsdomains[] = $url;
    }

    public function isUseCache(): bool
    {
        return false;
        //TODO отключение кеша ???
        if ($this->cache !== NULL) {
            return true;
        } else {
            return false;
        }
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
                    var_dump($res);
                }
                if ($res['error'] === NULL && $res['http_code'] == '200') {
                    $result = $res["response"];
                    $this->cache->set($key, $result);
                }
            }
        } else {
            //$res = array("error" => null, "response" => FALSE);
            $res = $this->request($url);
            if ($res['error'] === NULL) {
                $result = $res["response"];
            }
        }

        return $result;
    }

    protected function request($url): array
    {
        $result = array("error" => null, "response" => FALSE, "http_code" => '200');
        $headers = array(
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            //"Accept-Encoding: gzip, deflate",
            "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
            "Cache-Control: max-age=0",
            "Connection: keep-alive",
            "x-client-id: b6c117e5-ae61-4ef5-b4cc-e0b1e37f0631"
        );
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

                $result["response"] = curl_exec($curl);
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
}
