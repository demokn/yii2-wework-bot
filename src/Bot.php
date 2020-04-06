<?php

namespace demokn\weworkbot;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Bot extends Component
{
    public $webhook;

    public $testMode = false;

    public function init()
    {
        parent::init();

        if ($this->webhook === null) {
            throw new InvalidConfigException('The "webhook" property must be set.');
        }
    }

    /**
     * @param  string|array $message
     * @return array|string
     */
    public function sendOrFail($message)
    {
        $canonicalMessage = $this->composeMessage($message);
        if ($this->testMode) {
            Yii::info(__METHOD__.' in testMode, the message is '.json_encode($canonicalMessage, JSON_UNESCAPED_UNICODE));
            $response = ['errcode' => 0, 'errmsg' => 'ok'];
        } else {
            $response = $this->postJson($this->webhook, $canonicalMessage);
        }

        if (!is_array($response) || !isset($response['errcode'])) {
            throw new \RuntimeException('Invalid response '.var_export($response, true));
        }
        if ($response['errcode'] !== 0) {
            throw new \RuntimeException('消息发送失败 '.var_export($response, true));
        }

        return $response;
    }

    /**
     * @param  string|array      $message
     * @return array|bool|string
     */
    public function send($message)
    {
        try {
            return $this->sendOrFail($message);
        } catch (\Throwable $e) {
            Yii::warning($e);
        }

        return false;
    }

    protected function composeMessage($message): array
    {
        if (is_string($message)) {
            return [
                'msgtype' => 'text',
                'text' => [
                    'content' => mb_substr($message, 0, 2048),
                ],
            ];
        } elseif (is_array($message)) {
            return $message;
        }

        throw new \InvalidArgumentException('Invalid message type.');
    }

    protected function getHttpClient(array $options = []): Client
    {
        return new Client($options);
    }

    /**
     * @param  string|UriInterface $endpoint
     * @param  array               $params
     * @param  array               $headers
     * @return array|string
     */
    protected function postJson($endpoint, $params = [], $headers = [])
    {
        $response = $this->getHttpClient()->post($endpoint, [
            'headers' => $headers,
            'json' => $params,
        ]);

        return $this->unwrapResponse($response);
    }

    /**
     * @return array|string
     */
    protected function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();
        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents)), true);
        }

        return $contents;
    }
}
