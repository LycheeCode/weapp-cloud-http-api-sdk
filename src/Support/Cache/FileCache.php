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
