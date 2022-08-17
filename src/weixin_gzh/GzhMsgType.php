<?php

namespace shiyunSdk\weixin_gzh;

class GzhMsgType extends GzhBase
{
    use TraitBaseRev;
    private $_funcflag = false;
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
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'text',
            'Content' => $this->_auto_text_filter($text),
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
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
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'image',
            'Image' => array(
                'MediaId' => $mediaid
            ),
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
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
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'voice',
            'Voice' => array(
                'MediaId' => $mediaid
            ),
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
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
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'video',
            'Video' => array(
                'MediaId' => $mediaid,
                'Title' => $title,
                'Description' => $description
            ),
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     */
    public function music($title, $desc, $musicurl, $hgmusicurl = '', $thumbmediaid = '')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => 'music',
            'Music' => array(
                'Title' => $title,
                'Description' => $desc,
                'MusicUrl' => $musicurl,
                'HQMusicUrl' => $hgmusicurl
            ),
            'FuncFlag' => $FuncFlag
        );
        if ($thumbmediaid) {
            $msg['Music']['ThumbMediaId'] = $thumbmediaid;
        }
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
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $count = count($newsData);

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => 'news',
            'CreateTime' => time(),
            'ArticleCount' => $count,
            'Articles' => $newsData,
            'FuncFlag' => $FuncFlag
        );
        $this->Message($msg);
        return $this;
    }
}
