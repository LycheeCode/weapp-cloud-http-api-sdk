<?php

namespace Lychee\Cloud\Base;

use Psr\SimpleCache\CacheInterface;

class AccessTokenStorage
{
    public const KEY_PREFIX = 'access_token:';

    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function store(string $appid, string $token, $expire_in = 7200)
    {
        $this->cache->set(
            KEY_PREFIX . md5($appid),
            $token,
            $expire_in
        );
    }

    public function retrieve(string $appid)
    {
        return $this->cache->get(
            KEY_PREFIX . md5($appid),
        );
    }
}
