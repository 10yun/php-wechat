<?php


class WxJssdk2
{



    /**
     * 获取JsApi使用签名
     * @param string $url 网页的URL，自动处理#及其后面部分
     * @param string $timestamp 当前时间戳 (为空则自动生成)
     * @param string $noncestr 随机串 (为空则自动生成)
     * @param string $appid 用于多个appid时使用,可空
     * @return array|bool 返回签名字串
     */
    public function getJsSign($url, $timestamp = 0, $noncestr = '', $appid = '')
    {
        if (!$this->jsapi_ticket && !$this->getJsApiTicket($appid) || !$url)
            return false;

        if (!$timestamp)
            $timestamp = time();
        if (!$noncestr)
            $noncestr = $this->generateNonceStr();
        $ret = strpos($url, '#');
        if ($ret)
            $url = substr($url, 0, $ret);
        $url = trim($url);
        if (empty($url))
            return false;
        $arrdata = array(
            "timestamp" => $timestamp,
            "noncestr" => $noncestr,
            "url" => $url,
            "jsapi_ticket" => $this->jsapi_ticket
        );
        $sign = $this->getSignature($arrdata);
        if (!$sign)
            return false;
        $signPackage = array(
            "appid" => $this->_appID,
            "noncestr" => $noncestr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $sign
        );
        return $signPackage;
    }


    public function getSignPackage()
    {
        /**
         * 方式3
         */
        // $appid = $this->appId;
        // $url = $this->url;
        // if(! $appid || ! $this->token || ! $url){
        // return FALSE;
        // }
        // // 处理超链接#
        // $ret = strpos ( $url, '#' );
        // if($ret){
        // $url = substr ( $url, 0, $ret );
        // }
        // $url = trim ( $url );
        /**
         * 方式1
         */
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $jsapiTicket = $this->getJsApiTicket($this->token);

        $timestamp = time();
        $nonceStr = $this->createNonceStr(16);

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }



    /**
     * 生成随机字串
     * @param number $length 长度，默认为16，最长为32字节
     * @return string
     */
    public function generateNonceStr($length = 16)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
    /**
     * 获取签名
     * @param array $arrdata 签名数组
     * @param string $method 签名方法
     * @return boolean|string 签名值
     */
    public function getSignature($arrdata, $method = "sha1")
    {
        if (!function_exists($method))
            return false;
        ksort($arrdata);
        $paramstring = "";
        foreach ($arrdata as $key => $value) {
            if (strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $Sign = $method($paramstring);
        return $Sign;
    }
}
