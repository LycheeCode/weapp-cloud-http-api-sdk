# 小程序云开发 HTTP API SDK

*⚠️ 开发测试中，功能残缺且不稳定，不建议用于生产环境*

# 快速上手

安装：

```bash
composer require lychee/weapp-cloud-sdk
```

初始化：

```PHP
<?php

use Lychee\Cloud\App;

$app = new App([
    'appid'     => 'YOUR_APPID',
    'appsecret' => 'YOUR_APPSECRET',
    'env'       => 'YOUR_ENV',
]);
```

## API

### AccessToken

获取 `access_token`：

```PHP
$access_token = $app->accessToken()->get();

// 如果本地缓存没有，自动从微信刷新 access_token
$access_token = $app->accessToken()->get(true);

// 从微信刷新 access_token，返回值格式为微信响应（array）
$access_token = $app->accessToken()->refresh();
```

### 云数据库

获取 `Database` 类实例：

```PHP
$db = $app->database();
```

`Database` 类目前支持的方法：

 - 插入记录 `add(string $query)`
 - 查询记录 `query(string $query)`
 - 更新记录 `update(string $query)`
 - 删除记录 `delete(string $query)`