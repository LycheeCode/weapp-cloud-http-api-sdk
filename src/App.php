<?php

namespace Lychee\Cloud;

use Lychee\Cloud\Base\AccessToken;
use Lychee\Cloud\Base\AccessTokenStorage;
use Lychee\Cloud\Base\Database;
use Lychee\Cloud\Support\Cache\FileCache;
use Psr\SimpleCache\CacheInterface;

class App
{
    private $appid;
    private $appsecret;
    private $env;

    /**
     * 缓存管理器
     *
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    /**
     * AccessToken 管理器
     *
     * @var \Lychee\Cloud\Base\AccessToken
     */
    private $access_token;

    private $database;

    public function __construct(array $options = [])
    {
        if (! isset($options['appid'])) {
            throw new \Exception('appid is required');
        }

        if (! isset($options['appsecret'])) {
            throw new \Exception('appsecret is required');
        }

        if (! isset($options['env'])) {
            throw new \Exception('env is required');
        }

        $this->appid = $options['appid'];
        $this->appsecret = $options['appsecret'];
        $this->env = $options['env'];

        if (isset($options['cache'])) {
            if (! $options['cache'] instanceof CacheInterface) {
                throw new \Exception('cache must be instance of Psr\SimpleCache\CacheInterface');
            }

            $this->cache = $options['cache'];
        } else {
            $this->cache = new FileCache();
        }

        $this->access_token = new AccessToken(
            new AccessTokenStorage($this->cache),
            $this->appid,
            $this->appsecret
        );
    }

    /**
     * AccessToken 管理器
     *
     * @return \Lychee\Cloud\Base\AccessToken
     */
    public function accessToken(): AccessToken
    {
        return $this->access_token;
    }

    /**
     * 云数据库操作类
     *
     * @return \Lychee\Cloud\Base\Database
     */
    public function database(): Database
    {
        if (is_null($this->database)) {
            $this->database = new Database($this->access_token, $this->env);
        }

        return $this->database;
    }
}
