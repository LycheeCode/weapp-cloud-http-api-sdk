<?php

namespace Lychee\Cloud\Support\Cache;

use Psr\SimpleCache\CacheInterface;
use Redis;

class RedisCache implements CacheInterface
{
    private $redis;

    private $prefix;

    /**
     * construct
     *
     * @param object $redis
     * @param string $prefix
     */
    public function __construct(Redis $redis_client, $prefix = 'cache:')
    {
        $this->redis = $redis_client;
        $this->prefix = $prefix;
    }

    /**
     * 获取缓存
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = $this->redis->get($this->prefix . $key);
        if (! $value) {
            $value = $default;
        }
        return json_decode($value, true);
    }

    /**
     * 设置缓存
     *
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return void
     */
    public function set($key, $value, $ttl = null)
    {
        $value = json_encode($value);
        if (! is_null($ttl)) {
            $this->redis->setex(
                $this->prefix . $key,
                $ttl,
                $value
            );
        } else {
            $this->redis->set(
                $this->prefix . $key,
                $value
            );
        }
    }

    /**
     * 删除指定 key
     *
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        $this->redis->del($key);
    }

    /**
     * 清空缓存
     *
     * @return bool
     */
    public function clear()
    {
        $iterator = null;
        do {
            $keys = $this->redis->scan($iterator, $this->prefix . "*");
            foreach ($keys as $key) {
                $this->delete($key);
            }
        } while ($iterator != 0);
        return true;
    }

    /**
     * 取得多个项
     *
     * @param array $keys
     * @param mixed $default
     * @return array
     */
    public function getMultiple($keys, $default = null)
    {
        $datas = [];
        foreach ($keys as $key) {
            $datas[$key] = $this->get($key, $default);
        }
        return $datas;
    }

    /**
     * 设置多个缓存
     *
     * @param array $values
     * @param null|int $ttl
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    /**
     * 删除多个项
     *
     * @param array $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * 某个 key 是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return !is_null($this->get($key));
    }
}
