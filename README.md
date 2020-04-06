# yii2-wework-bot

使用企业微信群机器人发送消息， 参考企业微信官方文档[群机器人配置说明](https://work.weixin.qq.com/api/doc/90000/90136/91770)

## 要求

- php: ^7.2
- yiisoft/yii2: ~2.0.14
- guzzlehttp/guzzle: ~6.3

## 安装

```shell
composer require "demokn/yii2-wework-bot:~1.0"
```

## 配置

```php
'components' => [
    // ...
    // 注册组件
    'weworkBot' => [
        'class' => \demokn\weworkbot\Bot::class,
        'webhook' => 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=YOUR_SECRET_KEY',
        'testMode' => !YII_ENV_PROD,
     ],
    'anotherWeworkBot' => [
        'class' => \demokn\weworkbot\Bot::class,
        'webhook' => 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=YOUR_SECRET_KEY',
        'testMode' => !YII_ENV_PROD,
     ],
    // ...
],
```

## 使用

```php
$bot = Yii::$app->weworkBot;

// 发送文本消息
$bot->send('这是一条文本消息');

// 发送复杂文本消息
$bot->send([
    'msgtype' => 'text',
    'text' => [
        'content' => '广州今日天气：29度，大部分多云，降雨概率：60%',
        'mentioned_list' => ['wangqing', '@all'],
        'mentioned_mobile_list' => ['13800001111', '@all'],
    ],
]);

// 发送markdown消息
$bot->send([
    'msgtype' => 'markdown',
    'markdown' => [
        'content' => "实时新增用户反馈<font color=\"warning\">132例</font>，请相关同事注意。\n
>类型:<font color=\"comment\">用户反馈</font>\n
>普通用户反馈:<font color=\"comment\">117例</font>\n
>VIP用户反馈:<font color=\"comment\">15例</font>",
    ],
]);
// ...
```

更多消息类型，参考企业微信官方文档[群机器人配置说明](https://work.weixin.qq.com/api/doc/90000/90136/91770)

## License

MIT
