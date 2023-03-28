<?php
/**
 * ChatGpt 调用实现
 * @Author: wzq
 * @Date: 2023/03/09
 */

namespace ChatGPTGateway;

class ChatGpt
{
    use Traits\Request; //请求实现
    use Traits\Translate; //翻译
    use Traits\Text; //文本处理
    use Traits\Comment; //评论相关

    //配置
    public $config = [];

    //聊天记录
    public $history = [];

    //api 服务器地址
    private $apiHost = 'https://api.openai.com';

    //是否开启 debug 模式
    public $debug = false;

    public function __construct($appKey, $appSecret, $config = [])
    {
        $this->config = array_merge($config, [
            'appKey'    => $appKey,
            'appSecret' => $appSecret,
        ]);

        if (!empty($this->config['apiHost'])) {
            $this->apiHost = $this->config['apiHost'];
        }
    }

    /**
     * 提问
     *
     * @param string $content
     * @param array $options
     * @return array
     */
    public function ask(string $content, array $options = []): array
    {
        //请求接口
        $response = $this->request('/api/open/query', array_merge([
            'model'   => 'gpt-3.5-turbo',
            'content' => $content,
            // 'stream'   => true,
        ], $options), 'POST', [
            // 'stream' => true,
        ]);

        $result = @json_decode((string) $response->getBody(), true);

        if ($result['status'] != 'ok') {
            return [];
        }

        return $result;
    }

    /**
     * 记录历史数据
     *
     * @param string $role
     * @param string $content
     * @return void
     */
    public function addHistory(string $role, string $content)
    {
        $this->history[] = [
            'role'    => $role,
            'content' => $content,
        ];

        $limit = $this->config['history_limit'] ?? 0;

        //如限制了历史记录最大数则进行删除
        if ($limit > 0 && count($this->history) > $limit) {
            array_shift($this->history);
        }
    }

    /**
     * 清除历史数据
     *
     * @return void
     */
    public function clearHistory()
    {
        $this->history = [];
    }

    /**
     * 获取消息文本
     *
     * @param array $result
     * @return string
     */
    public function getMessageContent(array $result)
    {
        return trim($result['data']['content'] ?? '');
    }

    /**
     * 获取对话 id
     *
     * @param array $result
     * @return string
     */
    public function getChatId(array $result)
    {
        return trim($result['data']['chat_id'] ?? '');
    }
}
