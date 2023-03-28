<?php
/**
 * 文本相关实现
 * @Author: wzq
 * @Date: 2023/03/09
 */

 namespace ChatGPTGateway\Traits;

trait Text
{
    /**
     * 文本纠错
     *
     * @param string $text
     * @return array
     */
    public function correction(string $text)
    {
        if (empty($text)) {
            return [];
        }

        //组装问题内容
        $content = "下面的文本中的错别字有哪些？“{$text}” 请将错别字使用 json 进行输出，例如：[\"original\":\"原文本\"，\"correct\":\"正确文本\"]";

        //请求 chatGPT
        $result = $this->ask($content, []);

        //获取消息文本
        $data = $this->getMessageContent($result);

        $data = @json_decode($data, true);

        if (!isset($data[0]['original'])) {
            return [];
        }

        return [$data, $this->getChatId($result)];
    }

    /**
     * 文本润色
     *
     * @param string $text
     * @return string
     */
    public function optimize(string $text)
    {
        if (empty($text)) {
            return null;
        }

        //组装问题内容
        $content = "帮我把下面的文本进行润色并仅输出润色后的文本：“{$text}”";

        //请求 chatGPT
        $result = $this->ask($content);

        //获取消息文本
        return [$this->getMessageContent($result), $this->getChatId($result)];
    }

    /**
     * 基于主题写作
     *
     * @param string $text
     * @return string
     */
    public function writeByTitle(string $text, string $targetStyle = "一篇文章")
    {
        if (empty($text)) {
            return null;
        }

        //组装问题内容
        $content = "基于下面的主题帮我写{$targetStyle}：“{$text}”";

        //请求 chatGPT
        $result = $this->ask($content);

        //获取消息文本
        return [$this->getMessageContent($result), $this->getChatId($result)];
    }
}
