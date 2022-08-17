<?php

namespace shiyunSdk\weixin_qyh;

class QyhMsgType extends QyhBase
{
    private $_text_filter = true;

    /**
     * 过滤文字回复\r\n换行符
     * @param string $text
     * @return string|mixed
     */
    protected function _auto_text_filter($text)
    {
        if (!$this->_text_filter)
            return $text;
        return str_replace("\r\n", "\n", $text);
    }

    /**
     * 设置回复消息
     * Example: $obj->text('hello')->reply();
     * @param string $text
     */
    public function text($text = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'text',
            'Content' => $this->_auto_text_filter($text),
            'CreateTime' => time()
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->image('media_id')->reply();
     * @param string $mediaid
     */
    public function image($mediaid = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'image',
            'Image' => array(
                'MediaId' => $mediaid
            ),
            'CreateTime' => time()
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->voice('media_id')->reply();
     * @param string $mediaid
     */
    public function voice($mediaid = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'voice',
            'Voice' => array(
                'MediaId' => $mediaid
            ),
            'CreateTime' => time()
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->video('media_id','title','description')->reply();
     * @param string $mediaid
     */
    public function video($mediaid = '', $title = '', $description = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'video',
            'Video' => array(
                'MediaId' => $mediaid,
                'Title' => $title,
                'Description' => $description
            ),
            'CreateTime' => time()
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *    "0"=>array(
     *        'Title'=>'msg title',
     *        'Description'=>'summary text',
     *        'PicUrl'=>'http://www.domain.com/1.jpg',
     *        'Url'=>'http://www.domain.com/1.html'
     *    ),
     *    "1"=>....
     *  )
     */
    public function news($newsData = array())
    {
        $count = count($newsData);

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'news',
            'CreateTime' => time(),
            'ArticleCount' => $count,
            'Articles' => $newsData
        );
        $this->Message($msg);
        return $this;
    }
}
