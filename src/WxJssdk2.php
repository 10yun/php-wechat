<?php


class WxJssdk2
{
    /**
     * 删除JSAPI授权TICKET
     * @param string $appid 用于多个appid时使用
     */
    public function resetJsTicket($appid = '')
    {
        if (!$appid)
            $appid = $this->_appID;
        $this->jsapi_ticket = '';
        $authname = 'wechat_jsapi_ticket' . $appid;
        $this->removeCache($authname);
        return true;
    }
    /**
     * 获取JSAPI授权TICKET
     * @param string $appid 用于多个appid时使用,可空
     * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
     */
    public function getJsTicket($appid = '', $jsapi_ticket = '')
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        if (!$appid)
            $appid = $this->_appID;
        if ($jsapi_ticket) { // 手动指定token，优先使用
            $this->jsapi_ticket = $jsapi_ticket;
            return $this->jsapi_ticket;
        }
        $authname = 'wechat_jsapi_ticket' . $appid;
        if ($rs = $this->getCache($authname)) {
            $this->jsapi_ticket = $rs;
            return $rs;
        }
        $result = $this->curlHttpGet(
            self::URL_API_PREFIX .  'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $this->access_token . '&type=jsapi'
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->jsapi_ticket = $json['ticket'];
            $expire = $json['expires_in'] ? intval($json['expires_in']) - 100 : 3600;
            $this->setCache($authname, $this->jsapi_ticket, $expire);
            return $this->jsapi_ticket;
        }
        return false;
    }

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
        if (!$this->jsapi_ticket && !$this->getJsTicket($appid) || !$url)
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
