<?php

namespace Lychee\Cloud\Support\Cache;

use Psr\SimpleCache\CacheInterface;

class FileCache implements CacheInterface
{
    private $path;

    private $prefix;

    private $index_file;

    public function __construct(string $prefix = 'lychee_cache_')
    {
        $this->path = sys_get_temp_dir();

        $this->prefix = $prefix;

        /**
         * TODO: 将 $ttl != null 的 key 存储到 index_file 中，以便清理
         *
         *   因为当前的 FileCache 实现只用于缓存 Access Token，短期内不会造成缓存文件成堆，
         *   估暂不实现清理功能，后续需要实清理时需要考虑以下场景：
         *         1. 给一个不存在/不过期/已过期（等效于不存在）的 key 设置 ttl
         *         2. 未过期的 key，转为了不过期（$ttl == null）
         *         3. 未过期的 key，再次 set 并更新了 ttl
         */
        $this->index_file = fopen(
            $this->path . '/' . $this->prefix . 'index',
            'w+'
        );
    }

    public function __destruct()
    {
        fclose($this->index_file);
    }

    public function get($key, $default = null)
    {
        $file = $this->path . '/' . $this->prefix  . 'data_' . $key;

        if (!file_exists($file)) {
            return $default;
        }

        $data = json_decode(file_get_contents($file), true);

        if (! is_null($data['expires']) && time() > $data['expires']) {
            $this->delete($key);

            return $default;
        }

        return unserialize($data['value']);
    }

    public function set($key, $value, $ttl = null)
    {
        $file = $this->path . '/' . $this->prefix  . 'data_' . $key;

        $data = [
            'value'   => serialize($value),
            'expires' => is_null($ttl) ? null : time() + $ttl
        ];

        file_put_contents($file, json_encode($data));

        // TODO: $this->updateIndex($key);

        return true;
    }

    public function delete($key)
    {
        $file = $this->path . '/' . $this->prefix  . 'data_' . $key;

        if (file_exists($file)) {
            unlink($file);
        }

        // TODO: $this->updateIndex($key, true);

        return true;
    }

    public function clear()
    {
        $files = glob($this->path . '/' . $this->prefix . 'data_*');

        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has($key)
    {
        $file = $this->path . '/' . $this->prefix  . 'data_' . $key;

        if (! file_exists($file)) {
            return false;
        }

        $data = json_decode(file_get_contents($file), true);

        if (! is_null($data['expires']) && time() > $data['expires']) {
            $this->delete($key);

            return false;
        }

        return true;
    }
}
