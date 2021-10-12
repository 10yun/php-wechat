<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\libs\Prpcrypt;

class GzhReply extends GzhBase
{
    /**
     * 
     * 回复微信服务器, 此函数支持链式操作
     * Example: $this->text('msg tips')->reply();
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function reply($msg = array(), $return = false)
    {
        if (empty($msg))
            $msg = $this->_msg;
        $xmldata = $this->xml_encode($msg);
        $this->log($xmldata);
        $pc = new Prpcrypt($this->encodingAesKey);
        $array = $pc->encrypt($xmldata, $this->_appID);
        $ret = $array[0];
        if ($ret != 0) {
            $this->log('encrypt err!');
            return false;
        }
        $timestamp = time();
        $nonce = rand(77, 999) * rand(605, 888) * rand(11, 99);
        $encrypt = $array[1];
        $tmpArr = array(
            $this->token,
            $timestamp,
            $nonce,
            $encrypt
        ); // 比普通公众平台多了一个加密的密文
        sort($tmpArr, SORT_STRING);
        $signature = implode($tmpArr);
        $signature = sha1($signature);
        $smsg = $this->generate($encrypt, $signature, $timestamp, $nonce);
        $this->log($smsg);
        if ($return)
            return $smsg;
        elseif ($smsg) {
            echo $smsg;
            return true;
        } else
            return false;
    }
    public function reply2($msg = array(), $return = false)
    {
        if (empty($msg)) {
            if (empty($this->_msg)) // 防止不先设置回复内容，直接调用reply方法导致异常
                return false;
            $msg = $this->_msg;
        }
        $xmldata = $this->xml_encode($msg);
        $this->log($xmldata);
        if ($this->encrypt_type == 'aes') { // 如果来源消息为加密方式
            $pc = new Prpcrypt($this->encodingAesKey);
            $array = $pc->encrypt($xmldata, $this->_appID);
            $ret = $array[0];
            if ($ret != 0) {
                $this->log('encrypt err!');
                return false;
            }
            $timestamp = time();
            $nonce = rand(77, 999) * rand(605, 888) * rand(11, 99);
            $encrypt = $array[1];
            $tmpArr = array(
                $this->token,
                $timestamp,
                $nonce,
                $encrypt
            ); // 比普通公众平台多了一个加密的密文
            sort($tmpArr, SORT_STRING);
            $signature = implode($tmpArr);
            $signature = sha1($signature);
            $xmldata = $this->generate($encrypt, $signature, $timestamp, $nonce);
            $this->log($xmldata);
        }
        if ($return)
            return $xmldata;
        else
            echo $xmldata;
    }
    /**
     * xml格式加密，仅请求为加密方式时再用
     */
    private function generate($encrypt, $signature, $timestamp, $nonce)
    {
        // 格式化加密信息
        $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }
}
