<?php

namespace demokn\weworkbot\tests;

use demokn\weworkbot\Bot;

class BotTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->mockApplication([
            'components' => [
                'weworkBot' => [
                    'class' => Bot::class,
                    'webhook' => 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=YOUR_SECRET_KEY',
                    'testMode' => true,
                ],
                'anotherWeworkBot' => [
                    'class' => Bot::class,
                    'webhook' => 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=YOUR_SECRET_KEY',
                    'testMode' => true,
                ],
            ],
        ]);
    }

    /**
     * @return Bot
     */
    protected function getWeworkBotComponent()
    {
        return \Yii::$app->weworkBot;
    }

    public function testInstanceWeworkBotComponent()
    {
        $this->assertInstanceOf(Bot::class, $this->getWeworkBotComponent());
    }

    public function testSendSimpleTextMessage()
    {
        $response = $this->getWeworkBotComponent()->send('这是一条文本消息。');
        $this->assertIsArray($response);
        $this->assertArrayHasKey('errcode', $response);
        $this->assertEquals(0, $response['errcode']);
    }

    public function testSendTextMessage()
    {
        $response = $this->getWeworkBotComponent()->send([
            'msgtype' => 'text',
            'text' => [
                'content' => '广州今日天气：29度，大部分多云，降雨概率：60%',
                'mentioned_list' => ['wangqing', '@all'],
                'mentioned_mobile_list' => ['13800001111', '@all'],
            ],
        ]);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('errcode', $response);
        $this->assertEquals(0, $response['errcode']);
    }

    public function testSendMarkdownMessage()
    {
        $response = $this->getWeworkBotComponent()->send([
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => "实时新增用户反馈<font color=\"warning\">132例</font>，请相关同事注意。\n
>类型:<font color=\"comment\">用户反馈</font>\n
>普通用户反馈:<font color=\"comment\">117例</font>\n
>VIP用户反馈:<font color=\"comment\">15例</font>",
            ],
        ]);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('errcode', $response);
        $this->assertEquals(0, $response['errcode']);
    }
}
