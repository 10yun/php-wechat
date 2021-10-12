<?php

namespace shiyunSdk\wechatSdk;


/**
 * 【ctocode】      微信 - 基础类
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
class WechatBase
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
    /**
     * 一下是定义微信的回调方法
     */
    // @action:验证签名 一下是定义微信的回调方法
    public function valid($echostr)
    {
        $token = $this->_token;
        if (empty($token)) {
            exit('Token不能为空');
            // throw new Exception ( 'TOKEN is not defined!' );
        }
        // try
        // {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce
        );
        sort($tmpArr);
        // sort ( $tmpArr, SORT_STRING );
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            echo $echoStr;
            exit();
        }
        // }
        // catch ( Exception $e )
        // {
        // echo 'Message: ' . $e->getMessage ();
        // }
    }

    /****************************************************
     *  微信提交API方法，返回微信指定JSON
     *  通用请求微信接口 [ 微信通讯 Communication ]
     ****************************************************/
    protected function wxHttpsRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl,CURLOPT_HEADER,0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );

        $output = curl_exec($curl);
        $is_errno = curl_errno($curl);
        if ($is_errno) {
            return 'Errno' . $is_errno;
        }
        curl_close($curl);
        return $output;
    }
}
