<?php

use shiyunSdk\wechatSdk\libs\HelperStr;

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
    public function getSignPackage($url = '', $timestamp = 0, $noncestr = '', $appid = '')
    {
        /**
         * 方式3
         */
        // $appid = $this->appId;
        // $url = $this->url;
        // if (!$appid || !$this->token || !$url) {
        //     return FALSE;
        // }
        // // 处理超链接#
        // $ret = strpos($url, '#');
        // if ($ret) {
        //     $url = substr($url, 0, $ret);
        // }
        // $url = trim($url);
        /**
         * 方式1
         */
        // 注意 URL 一定要动态获取，不能 hardcode.

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (empty($url)) {
            return false;
        }
        $jsapiTicket = $this->getJsApiTicket();
        if (empty($jsapiTicket)) {
            return false;
        }
        if (!$timestamp)
            $timestamp = time();
        if (!$noncestr) {
            $nonceStr = HelperStr::createNonceStr(16);
        }

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);

        // $signature = $this->getSignature(array(
        //     "jsapi_ticket" => $jsapiTicket
        //     "noncestr" => $noncestr,
        //     "timestamp" => $timestamp,
        //     "url" => $url,
        // ));

        if (!$signature)
            return false;

        $signPackage = array(
            "appid" => $this->_appID,
            "noncestr" => $noncestr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }
    /**
     * 获取签名
     * @param array $arrdata 签名数组
     * @param string $method 签名方法
     * @return boolean|string 签名值
     */
    public function getSignature($arrData, $method = "sha1")
    {
        if (!function_exists($method))
            return false;
        ksort($arrData);
        $paramStr = "";
        foreach ($arrData as $key => $value) {
            if (strlen($paramStr) == 0)
                $paramStr .= $key . "=" . $value;
            else
                $paramStr .= "&" . $key . "=" . $value;
        }
        $Sign = $method($paramStr);
        return $Sign;
    }
}
