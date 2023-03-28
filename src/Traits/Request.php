<?php
/**
 * 请求实现
 * @Author: wzq
 * @Date: 2023/03/09
 */

 namespace ChatGPTGateway\Traits;

use GuzzleHttp\Client;

trait Request
{
    /**
     * 接口请求
     *
     * @param string $uri
     * @param array $data
     * @param string $method
     * @param array $options
     * @return \GuzzleHttp\Client
     */
    private function request($uri, $data, $method = 'GET', $options = [], $requestBy = "guzzle")
    {
        $url = $uri;

        if (strpos($uri, 'http') !== 0) {
            $url = $this->apiHost . $uri;
        }

        //增加请求头部
        $headers = [
            'Authorization'  => 'Bearer ' . $this->getBearerToken(),
            'Applicationkey' => $this->config['appKey'],
        ];

        if ($this->config['encrypt'] ?? false) {
            $headers['EncryptBody'] = 1;
        }

        $options['headers'] = array_merge($options['headers'] ?? $headers);

        return $this->requestByGuzzle($url, $data, $method, $options);
    }

    /**
     * 通过 guzzle 请求
     *
     * @param string $url
     * @param array $data
     * @param string $method
     * @param array $options
     * @return void
     */
    private function requestByGuzzle($url, $data, $method = 'GET', $options = [])
    {
        //请求超时时间
        $timeout = $options['timeout'] ?? ($this->config['reuqest_timeout'] ?? 0);

        if (!empty($timeout)) {
            $options['timeout']         = $timeout;
            $options['connect_timeout'] = $timeout;
        }

        if (strtoupper($method) === 'GET' && !empty($data)) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($data);
            $data = [];
        }

        if (strtoupper($method) === 'POST' && !empty($data)) {
            if ($options['headers']['EncryptBody'] ?? 0) {
                $options['body'] = $this->encrypt(json_encode($data));
            } else {
                $options['form_params'] = $data;
            }
        }

        //设置代理
        if (isset($this->config['proxy'])) {
            $options['proxy'] = $this->config['proxy'];
        }

        $options['verify'] = false;

        if ($this->debug) {
            print_r('<pre>' . PHP_EOL);
            print_r('----------------------- debug ------------------------' . PHP_EOL);
            print_r('发送数据（加密前）：' . PHP_EOL);
            print_r($data);
            print_r(PHP_EOL);
            print_r('请求内容：' . PHP_EOL);
            print_r($options);
            print_r(PHP_EOL);
        }

        $client   = new Client();
        $response = $client->request(strtoupper($method), $url, $options);

        if ($this->debug) {
            print_r('响应内容：' . PHP_EOL);
            print_r((string) $response->getBody() . PHP_EOL);
            print_r('----------------------- debug end -------------------' . PHP_EOL);
            print_r('</pre>' . PHP_EOL);
        }

        return $response;
    }

    /**
     * 获取加密的 token
     *
     * @return string
     */
    private function getBearerToken()
    {
        //加密 appKey
        return $this->encrypt($this->config['appKey']);
    }

    /**
     * 加密数据
     *
     * @param string $data
     * @return string
     */
    private function encrypt(string $data): string
    {
        //加密数据
        return @\openssl_encrypt($data, 'AES-256-CBC', str_replace('-', '', $this->config['appSecret']));
    }

    /**
     * 开启 debug 模式
     *
     * @param integer $debug
     * @return Calss
     */
    public function debug($debug = 1)
    {
        $this->debug = (bool) $debug;

        return $this;
    }
}
