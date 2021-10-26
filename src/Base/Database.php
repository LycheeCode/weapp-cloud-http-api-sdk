<?php

namespace Lychee\Cloud\Base;

use Lychee\Cloud\Base\AccessToken;
use Lychee\Cloud\Support\Http\Constants\Type;
use Lychee\Cloud\Support\Http\Request;

class Database
{
    private $at_manager;

    private $env;

    private $client;

    public function __construct(AccessToken $at_manager, string $env)
    {
        $this->at_manager = $at_manager;

        $this->env = $env;

        $this->client = new Request();
    }

    public function add(string $query)
    {
        $url = sprintf(
            "https://api.weixin.qq.com/tcb/databaseadd?access_token=%s",
            $this->at_manager->get(true)
        );

        $query = [
            'env'   => $this->env,
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

    public function query(string $query)
    {
        $url = sprintf(
            "https://api.weixin.qq.com/tcb/databasequery?access_token=%s",
            $this->at_manager->get(true)
        );

        $query = [
            'env'   => $this->env,
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

    public function update(string $query)
    {
        $url = sprintf(
            "https://api.weixin.qq.com/tcb/databaseupdate?access_token=%s",
            $this->at_manager->get(true)
        );

        $query = [
            'env'   => $this->env,
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
