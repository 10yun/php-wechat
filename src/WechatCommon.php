<?php

namespace shiyunSdk\wechatSdk;

/**
 * 【ctocode】      微信 - 常用类
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 */
/**
 * @link http://mp.weixin.qq.com/wiki/home/index.html
 * @package 微信API接口 陆续会继续进行更新
 */

// ===================================获取 微信 用户名字===========================================
/**
 * 微信后台代码，自动更新access token，并存储到数据库，从而利用access token，在用户关注或发送消息时，获取用户昵称等信息
 */
class WechatCommon extends WechatBase
{
    /*
	 * 获取accesstoken
	 * 【请勿时时调用】;上层应用通过该接口来获取wxAccessToken,并将其保存到数据库中,仅当该access_token失效时去获取.
	 */
    /****************************************************
     *  微信获取AccessToken 返回指定微信公众号的at信息
     ****************************************************/
    public function wxAccessToken($appId = NULL, $appSecret = NULL)
    {
        $appId = is_null($appId) ? $this->_appID : $appId;
        $appSecret = is_null($appSecret) ? $this->_appSecret : $appSecret;
        if (!empty($appId) && !empty($appSecret)) {
            $access_token = \think\facade\Cache::get('WEIXIN_ACCESS_TOKEN_' . $appId);
            if ($access_token) {
                return $access_token;
            }
            $url = self::URL_API_PREFIX . "/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecret;
            $result = $this->wxHttpsRequest($url);
            // print_r($result);
            $jsoninfo = json_decode($result, true);
            $access_token = isset($jsoninfo["access_token"]) ? $jsoninfo["access_token"] : null;
            $expires_in = isset($jsoninfo["expires_in"]) ? $jsoninfo["expires_in"] - 600 : 0; // 有效时间，单位：秒(安全起见，提前10分钟)
            if ($access_token) { // 将$access_token存缓存，设置有效期
                \think\facade\Cache::set('WEIXIN_ACCESS_TOKEN_' . $appId, $access_token, $expires_in);
            }
            $this->access_token = $access_token;
            return $access_token;
        } else {
            return $this->access_token;
        }
    }
    /**
     * GET 请求
     * @param string $url
     */
    private function wxAccessToken2()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $cacheData = trim(substr(file_get_contents(__DIR__ . '/access_token.php'), 15));
        $data = json_decode($cacheData);
        // var_dump($data);
        // if(! Cache::has ( 'access_token' )){
        if ($data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            // echo $this->appId,$this->appSecret;
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                // Cache::set ( 'access_token', $access_token, 10 );
                $this->set_php_file("access_token.php", json_encode($data));
            }
        } else {
            // $access_token = Cache::get ( 'access_token' );
            $access_token = $data->access_token;
        }
        return $access_token;
    }
    /****************************************************
     *  微信获取ApiTicket 返回指定微信公众号的at信息
     ****************************************************/
    public function wxJsApiTicket($appId = NULL, $appSecret = NULL)
    {
        $appId = is_null($appId) ? $this->_appID : $appId;
        $appSecret = is_null($appSecret) ? $this->_appSecret : $appSecret;

        $wxAccessToken = $this->wxAccessToken();

        $url = self::URL_API_PREFIX . "/ticket/getticket?type=jsapi&access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        $ticket = $jsoninfo['ticket'];
        // echo $ticket . "<br >";
        return $ticket;
    }
    public function wxVerifyJsApiTicket($appId = NULL, $appSecret = NULL)
    {
        if (!empty($this->jsApiTime) && intval($this->jsApiTime) > time() && !empty($this->jsApiTicket)) {
            $ticket = $this->jsApiTicket;
        } else {
            $ticket = $this->wxJsApiTicket($appId, $appSecret);
            $this->jsApiTicket = $ticket;
            $this->jsApiTime = time() + 7200;
        }
        return $ticket;
    }

    /****************************************************
     *  微信通过OPENID获取用户信息，返回数组
     ****************************************************/
    public function wxGetUser($openId)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/user/info?access_token=" . $wxAccessToken . "&openid=" . $openId . "&lang=zh_CN";
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  微信生成二维码ticket
     ****************************************************/
    public function wxQrCodeTicket($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/qrcode/create?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        return $result;
    }

    /****************************************************
     *  微信通过ticket生成二维码
     ****************************************************/
    public function wxQrCode($ticket)
    {
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);
        return $url;
    }

    /*****************************************************
     *      生成随机字符串 - 最长为32位字符串
     *****************************************************/
    public function wxNonceStr($length = 16, $type = FALSE)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        if ($type == TRUE) {
            return strtoupper(md5(time() . $str));
        } else {
            return $str;
        }
    }

    /*******************************************************
     *      微信商户订单号 - 最长28位字符串
     *******************************************************/
    public function wxMchBillno($mchid = NULL)
    {
        if (is_null($mchid)) {
            if (self::mchid == "" || is_null(self::mchid)) {
                $mchid = time();
            } else {
                $mchid = self::mchid;
            }
        } else {
            $mchid = substr(addslashes($mchid), 0, 10);
        }
        return date("Ymd", time()) . time() . $mchid;
    }

    /*******************************************************
     *      微信格式化数组变成参数格式 - 支持url加密
     *******************************************************/
    public function wxSetParam($parameters)
    {
        if (is_array($parameters) && !empty($parameters)) {
            $this->parameters = $parameters;
            return $this->parameters;
        } else {
            return array();
        }
    }

    /*******************************************************
     *      微信格式化数组变成参数格式 - 支持url加密
     *******************************************************/
    public function wxFormatArray($parameters = NULL, $urlencode = FALSE)
    {
        if (is_null($parameters)) {
            $parameters = $this->parameters;
        }
        $restr = ""; // 初始化空
        ksort($parameters); // 排序参数
        foreach ($parameters as $k => $v) { // 循环定制参数
            if (null != $v && "null" != $v && "sign" != $k) {
                if ($urlencode) { // 如果参数需要增加URL加密就增加，不需要则不需要
                    $v = urlencode($v);
                }
                $restr .= $k . "=" . $v . "&"; // 返回完整字符串
            }
        }
        if (strlen($restr) > 0) { // 如果存在数据则将最后“&”删除
            $restr = substr($restr, 0, strlen($restr) - 1);
        }
        return $restr; // 返回字符串
    }

    /*******************************************************
     *      微信MD5签名生成器 - 需要将参数数组转化成为字符串[wxFormatArray方法]
     *******************************************************/
    public function wxMd5Sign($content, $privatekey)
    {
        try {
            if (is_null($privatekey)) {
                throw new Exception("财付通签名key不能为空！");
            }
            if (is_null($content)) {
                throw new Exception("财付通签名内容不能为空");
            }
            $signStr = $content . "&key=" . $privatekey;
            return strtoupper(md5($signStr));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /*******************************************************
     *      微信Sha1签名生成器 - 需要将参数数组转化成为字符串[wxFormatArray方法]
     *******************************************************/
    public function wxSha1Sign($content)
    {
        try {
            if (is_null($content)) {
                throw new Exception("签名内容不能为空");
            }
            // $signStr = $content;
            return sha1($content);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /*******************************************************
     *      微信jsApi整合方法 - 通过调用此方法获得jsapi数据
     *******************************************************/
    public function wxJsapiPackage()
    {
        $jsapi_ticket = $this->wxVerifyJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

        $timestamp = time();
        $nonceStr = $this->wxNonceStr();

        $signPackage = array(
            "jsapi_ticket" => $jsapi_ticket,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url
        );

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $rawString = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        // $rawString = $this->wxFormatArray($signPackage);
        $signature = $this->wxSha1Sign($rawString);

        $signPackage['signature'] = $signature;
        $signPackage['rawString'] = $rawString;
        $signPackage['appId'] = $this->_appID;

        return $signPackage;
    }
    /*******************************************************
     *      将数组解析XML - 微信红包接口
     *******************************************************/
    public function wxArrayToXml($parameters = NULL)
    {
        if (is_null($parameters)) {
            $parameters = $this->parameters;
        }

        if (!is_array($parameters) || empty($parameters)) {
            die("参数不为数组无法解析");
        }

        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }
}
