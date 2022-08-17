<?php

namespace shiyunWechat;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\libs\HelperCache;

/**
 * 【ctocode】      微信 - 常用类
 * ============================================================================
 * @author       作者      ctocode
 * @version 	 版本	   v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */

class WechatCommon
{
    const URL_API_BASE_PREFIX = 'https://api.weixin.qq.com'; // 以下API接口URL需要使用此前缀
    const URL_API_PREFIX = 'https://api.weixin.qq.com/cgi-bin';


    // 原始ID 申请公共号时系统给你的唯一编号，有此 i
    protected $_WX_ID = ''; // 微信号 original
    protected $_originalID = ''; // 微信号 original
    protected $_appID = ''; // 开通 api 服务时，系统给你的唯一编号
    protected $_appSecret = ''; // 开通 api 服务时你注册的用户名
    protected $_token = ''; // Token(令牌),系统临时发放的身份识别字
    public $access_token = ''; // 调用接口凭证
    public $business_id = ''; // 商户id

    // ENCODINGAESKEY AES 加密时的密钥
    protected $privatekey = ''; // 私钥
    protected $parameters = array();
    protected $jsApiTicket = NULL;
    protected $jsApiTime = NULL;
    // 构造函数
    public function __construct($config = array())
    {
        $this->_appID = !empty($config['appid']) ? $config['appid'] : '';
        $this->_appSecret = !empty($config['appsecret']) ? $config['appsecret'] : '';
        $this->_token = isset($config['token']) ? $config['token'] : '';
        $this->access_token = !empty($config['access_token']) ? $config['access_token'] : '';
        $this->business_id = !empty($config['business_id']) ? $config['business_id'] : 0;
    }
    public function setAppId($str = '')
    {
        $this->_appID = $str;
        return $this;
    }
    public function setAppSecret($str = '')
    {
        $this->_appSecret = $str;
        return $this;
    }
    public function getToken()
    {
        $this->wxAccessToken();
        return $this;
    }
    /**
     * 获取access_token
     * 【请勿时时调用】;上层应用通过该接口来获取wxAccessToken,
     * 并将其保存到数据库中,仅当该access_token失效时去获取.
     * @param string $token 手动指定access_token，非必要情况不建议用
     */
    /****************************************************
     *  微信获取AccessToken 返回指定微信公众号的at信息
     ****************************************************/
    public function wxAccessToken($token = '')
    {
        if ($token) { // 手动指定token，优先使用
            $this->access_token = $token;
            return $this->access_token;
        }
        if (empty($this->_appID) && empty($this->_appSecret)) {
            return $this->access_token;
        }
        $cache_name = $this->cache_data_sign . $this->_appID;
        $cache_arr = HelperCache::getCache($cache_name);
        if ($cache_arr['access_token']) {
            return $cache_arr['access_token'];
        }
        // 如果是企业号用以下URL获取access_token
        // $url = self::URL_API_PREFIX ."/gettoken?corpid={$this->_appID}&corpsecret={$this->_appSecret}";

        $url = self::URL_API_PREFIX . "/token?grant_type=client_credential&appid={$this->_appID}&secret={$this->_appSecret}";
        $result = HelperCurl::curlHttpGet($url);

        if ($result) {
            $jsonInfo = json_decode($result, true);
            if (!$jsonInfo || isset($jsonInfo['errcode'])) {
                $this->errCode = $jsonInfo['errcode'];
                $this->errMsg = $jsonInfo['errmsg'];
                return false;
            }
            $access_token = isset($jsonInfo["access_token"]) ? $jsonInfo["access_token"] : null;
            // 有效时间，单位：秒(安全起见，提前10分钟)
            $expires_in = isset($jsonInfo["expires_in"]) ? intval($jsonInfo['expires_in']) - 600 : 3600;
            // 将$access_token存缓存，设置有效期
            HelperCache::setCache($cache_name, $access_token, $expires_in);
            $this->access_token = $access_token;
        }
        return $this->access_token;
    }

    /****************************************************
     *  微信通过OPENID获取用户信息，返回数组
     ****************************************************/
    public function wxGetUser($openId)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/user/info?access_token={$wxAccToken}&openid=" . $openId . "&lang=zh_CN";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  微信生成二维码ticket
     ****************************************************/
    public function wxQrCodeTicket($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/qrcode/create?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
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
                throw new \Exception("财付通签名key不能为空！");
            }
            if (is_null($content)) {
                throw new \Exception("财付通签名内容不能为空");
            }
            $signStr = $content . "&key=" . $privatekey;
            return strtoupper(md5($signStr));
        } catch (\Exception $e) {
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
                throw new \Exception("签名内容不能为空");
            }
            // $signStr = $content;
            return sha1($content);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
