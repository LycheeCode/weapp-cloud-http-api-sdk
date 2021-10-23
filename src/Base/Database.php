<?php

namespace Lychee\Cloud\Base;

use Lychee\Cloud\Base\AccessToken;
use Lychee\Cloud\Support\Http\Constants\Type;
use Lychee\Cloud\Support\Http\Request;

class Database
{
    private $at_manager;

    private $client;

    public function __construct(AccessToken $at_manager)
    {
        $this->at_manager = $at_manager;

        $this->client = new Request();
    }

    public function add(string $env, string $query)
    {
        $url = sprintf(
            "https://api.weixin.qq.com/tcb/databaseadd?access_token=%s",
            $this->at_manager->get()
        );

        $query = [
            'env'   => $env,
            'query' => $query,
        ];
        $response = $this->client->postRaw(
            $url,
            json_encode($query, JSON_UNESCAPED_UNICODE),
            Type::JSON
        );

        $body = $response['body'];

        return json_decode($body, true);
    }

    public function query(string $env, string $query)
    {
        $url = sprintf(
            "https://api.weixin.qq.com/tcb/databasequery?access_token=%s",
            $this->at_manager->get()
        );

        $query = [
            'env'   => $env,
            'query' => $query,
        ];
        $response = $this->client->postRaw(
            $url,
            json_encode($query, JSON_UNESCAPED_UNICODE),
            Type::JSON
        );

        $body = $response['body'];

        return json_decode($body, true);
    }

    public function update($env, $query)
    {
        $url = sprintf(
            "https://api.weixin.qq.com/tcb/databaseupdate?access_token=%s",
            $this->at_manager->get()
        );

        $query = [
            'env'   => $env,
            'query' => $query,
        ];
        $response = $this->client->postRaw(
            $url,
            json_encode($query, JSON_UNESCAPED_UNICODE),
            Type::JSON
        );

        $body = $response['body'];

        return json_decode($body, true);
    }
}
