<?php

namespace Lychee\Cloud\Support\Http;

use Lychee\Cloud\Support\Http\Constants\Type;

class Request
{
    public $ua = "Lychee WeApp Cloud HTTP API Client";
    public $timeout = 5;

    public function __construct(array $options = [])
    {
        if (isset($options['ua']) && is_string($options['ua'])) {
            $this->ua = $options['ua'];
        }

        if (isset($options['timeout']) && is_int($options['timeout'])) {
            $this->timeout = $options['timeout'];
        }
    }

    /**
    * 发起 GET 请求
    *
    * @param string $url
    * @return array
    */
    public function get(string $url): array
    {
        $options = [
            CURLOPT_USERAGENT      => $this->ua,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_URL            => $url,
        ];

        return $this->request($options);
    }

    /**
     * POST 表单数据
     *
     * @param string $url
     * @param array $datas
     * @return array
     */
    public function postFormData(string $url, array $datas): array
    {
        $options = [
            CURLOPT_USERAGENT      => $this->ua,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => http_build_query($data)
        ];

        return $this->request($options);
    }

    /**
     * POST Raw
     *
     * @param string $url
     * @param string $raw_data
     * @param string $type
     * @return array
     */
    public function postRaw(string $url, string $raw_data, string $content_type = null): array
    {
        $options = [
            CURLOPT_USERAGENT      => $this->ua,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $raw_data,
            CURLOPT_HTTPHEADER     => is_null($content_type) ? ['Content-Type: ' . Type::TEXT] : ['Content-Type: ' . $content_type]
        ];

        return $this->request($options);
    }

    /**
     * HEAD 请求
     *
     * @param string $url
     * @return array
     */
    public function head(string $url): array
    {
        $options = [
            CURLOPT_USERAGENT      => $this->ua,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_URL            => $url
        ];

        return $this->request($options);
    }

    /**
     * CURL 请求
     *
     * @param array $options
     * @return array
     */
    private function request(array $options = []): array
    {
        $options[CURLOPT_HEADER] = true; // curl_exec() 返回响应头
        $options[CURLOPT_RETURNTRANSFER] = true;

        $client = curl_init();
        curl_setopt_array($client, $options);

        $result = curl_exec($client);

        $http_code = curl_getinfo($client, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($client, CURLINFO_HEADER_SIZE);

        curl_close($client);

        $headers_str = substr($result, 0, $header_size);
        $headers = [];
        foreach (explode("\r\n", $headers_str) as $item) {
            $item = explode(":", $item);
            if (count($item) < 2) {
                continue;
            }
            $key = $item[0];
            unset($item[0]);
            $key = str_replace('-', '_', $key);
            $headers[$key] = implode(":", $item);
        }

        $body = substr($result, $header_size);

        $response = [
            'code'    => $http_code,
            'headers' => $headers,
            'body'    => $body,
        ];

        return $response;
    }
}
