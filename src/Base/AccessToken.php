<?php

namespace Lychee\Cloud\Base;

use Lychee\Cloud\Support\Http\Request as Client;
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
        $this->client    = new Client();
    }

    public function get(bool $auto_refresh = false): string
    {
        $accessToken = $this->storage->retrieve($this->appid);

        if (empty($accessToken) && $auto_refresh) {
            $refresh = $this->refresh();
            if (isset($refresh['access_token'])) {
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

        $body = $response['body'];

        $res = json_decode($body, true);

        if (isset($res['access_token'])) {
            $this->set(
                $res['access_token'],
                $res['expires_in'] - 500
            );
            $accessToken = $res['access_token'];
        }

        return $res;
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
