<?php


namespace App\Modules\Shop\Parser;


class Cache
{
    protected string $cachPath = './cache/';

    public function setPath($path) {
        $this->cachPath = $path;
    }

    public function get($key) {
        $result = null;
        if (file_exists($this->cachPath . $key)) {
            $result = file_get_contents($this->cachPath . $key);
        }
        return $result;
    }

    public function set($key, $value) {
        file_put_contents($this->cachPath . $key, $value);
    }

    public function delete($key) {
        if (file_exists($this->cachPath . $key)) {
            unlink($this->cachPath . $key);
        }
    }

    /**
     *
     * @param string $key
     * @param int $time  Время возвращается в формате временной метки (Unix TimeStamp Unix)
     */
    public function isValid(string $key, int $time) {
        if (file_exists($this->cachPath . $key)) {
            $filetime = filemtime($this->cachPath . $key);
            if ((time() - $filetime) > $time) {
                unlink($this->cachPath . $key);
            }
        }
    }

    public function clearAll() {
        throw new \DomainException('Not implemented');
    }
}
