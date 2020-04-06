<?php

namespace demokn\weworkbot;

use yii\di\Instance;
use yii\log\Target;

class LogTarget extends Target
{
    /**
     * @var Bot
     */
    public $bot;

    public function init()
    {
        parent::init();

        $this->bot = Instance::ensure($this->bot, Bot::class);
    }

    public function export()
    {
        foreach ($this->messages as $message) {
            $this->bot->send($this->formatMessage($message));
        }
    }

    public function formatMessage($message)
    {
        $message = parent::formatMessage($message);

        // 只上报第一行就行了, 详情登入服务器查看
        return current(explode("\n", $message));
    }
}
