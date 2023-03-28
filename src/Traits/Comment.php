<?php
/**
 * 文本相关实现
 * @Author: wzq
 * @Date: 2023/03/09
 */

namespace ChatGPTGateway\Traits;

trait Comment
{
    /**
     * 评论文章
     *
     * @param string $text
     * @return array
     */
    public function commentArticle(string $text, $num = 5)
    {
        if (empty($text)) {
            return [];
        }

        //组装问题内容
        $content = "请帮我发{$num}条不同的优质评论，要求：1、评论需要口语化一点，不要太机械；2、评论字数在20-40个字以内就好；3、评论需与主题有关4、特别注意：每条评论不要用一样的句式！需要不同表达结构的。返回的结果请使用 json 数组。文章如下：{$text}";

        //请求 chatGPT
        $result = $this->ask($content, []);

        //获取消息文本
        $data = $this->getMessageContent($result);

        $data = @json_decode($data, true);

        if (!is_array($data) || empty($data)) {
            return [[], 0];
        }

        return [$data, $this->getChatId($result)];
    }
}
