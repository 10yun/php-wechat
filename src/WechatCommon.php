<?php

namespace shiyunSdk\wechatSdk;

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

    // openID
    const mchid = ""; // 商户号

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
