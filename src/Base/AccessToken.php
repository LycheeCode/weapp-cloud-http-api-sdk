<?php

namespace Lychee\Cloud\Base;

use GuzzleHttp\Client;
use Psr\SimpleCache\CacheInterface;

class AccessToken
{
    private $appid;
    private $appsecret;

    private $storage;

    private $client;

    public function __construct(AccessTokenStorage $storage, string $appid, string $appsecret)
    {
        $this->storage   = $storage;
        $this->appid     = $appid;
        $this->appsecret = $appsecret;
        $this->client    = new Client;
    }

    public function get(bool $force_refresh)
    {
        $accessToken = $this->storage->retrieve($this->appid);

        if (empty($accessToken) && $force_refresh) {
            $refresh = $this->refresh();
            if (isset($refresh['access_token'])) {
                $this->set(
                    $refresh['access_token'],
                    $refresh['expires_in'] - 500
                );
                $accessToken = $refresh['access_token'];
            } else {
                throw new \Exception($refresh['errmsg']);
            }
        }

        return $accessToken;
    }

    public function refresh()
    {
        $url = sprintf(
            "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",
            $this->appid,
            $this->appsecret
        );

        $response = $this->client->get($url);

        $body = $response->getBody()->getContents();

        return json_decode($body, true);
    }

    public function set(string $token, int $expire_in)
    {
        $this->storage->store(
            $this->appid,
            $token,
            $expire_in
        );

        return $this;
    }
}
