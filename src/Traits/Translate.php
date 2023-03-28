<?php
/**
 * 翻译实现
 * @Author: wzq
 * @Date: 2023/03/09
 */

 namespace ChatGPTGateway\Traits;

trait Translate
{
    /**
     * 翻译文本
     *
     * @param string $text
     * @param string $toLanguage
     * @return string
     */
    public function translate(string $text, string $toLanguage = '英文')
    {
        if (empty($toLanguage)) {
            return null;
        }

        //组装问题内容
        $content = "将下面的文本翻译成{$toLanguage}:{$text}";

        //请求 chatGPT
        $result = $this->ask($content);

        //获取消息文本
        return [$this->getMessageContent($result), $this->getChatId($result)];
    }
}
