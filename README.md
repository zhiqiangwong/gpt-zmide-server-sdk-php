# ChatGPT 网关 SDK

## 安装

```
composer require zhiqiangwong/gpt-zmide-server-sdk-php
```

## 使用

```
$chat = new ChatGpt('sk-cttbFUeDumoGQBuYMFFTOsbGXydxvDpkhgzhwrgDzWoCcZcB', 'sxqzNfBlOrMonqnsEqLNkjJUsDmADEfX', [
    'apiHost' => 'http://host.docker.internal:8091', //设置请求 host
    'encrypt' => true, //设置 body 加密传输
]);

$response = $chat->debug(1)->translate('你好世界');
```