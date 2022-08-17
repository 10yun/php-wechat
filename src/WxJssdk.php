<?php

namespace shiyunWechat;

use think\facade\Cache;

use shiyunWechat\libs\HelperCurl;
use shiyunWechat\libs\HelperCache;

/**
 * App优化 
 */
class WxJsSdk extends WechatCommon
{
    private $_appID;
    private $_appSecret;
    protected $cache_data_sign = 'wechat_jsapi_ticket';
    private $path;
    private $access_token; // access_token
    private $url;
    public function __construct($appId = '', $appSecret = '')
    {
        $this->_appID = $appId;
        $this->_appSecret = $appSecret;
        $this->path = __DIR__ . '/';

        $this->_appID = isset($options['appId']) ? $options['appId'] : '';
        $this->access_token = isset($options['token']) ? $options['token'] : '';
        $this->url = isset($options['url']) ? $options['url'] : '';
    }
    /*******************************************************
     *      微信jsApi整合方法 - 通过调用此方法获得jsapi数据
     *******************************************************/
    /**
     * 获取JsApi使用签名
     * @param string $url 网页的URL，自动处理#及其后面部分
     * @param string $timestamp 当前时间戳 (为空则自动生成)
     * @param string $noncestr 随机串 (为空则自动生成)
     * @param string $appid 用于多个appid时使用,可空
     * @return array|bool 返回签名字串
     */
    public function wxJsApiPackage()
    {

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        if (empty($url)) {
            return false;
        }

        $jsapi_ticket = $this->wxVerifyJsApiTicket();
        if (empty($jsapiTicket)) {
            return false;
        }

        if (empty($timestamp)) {
            $timestamp = time();
        }
        if (empty($noncestr)) {
            $nonceStr = HelperRandom::doNumLetter(16);
        }

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

        if (!$signature)
            return false;

        $signPackage['signature'] = $signature;
        $signPackage['rawString'] = $rawString;
        $signPackage['appId'] = $this->_appID;

        return $signPackage;
    }
    protected function wxVerifyJsApiTicket($appId = NULL, $appSecret = NULL)
    {
        if (!empty($this->jsApiTime) && intval($this->jsApiTime) > time() && !empty($this->jsApiTicket)) {
            $ticket = $this->jsApiTicket;
        } else {
            $ticket = $this->getJsApiTicket($appId, $appSecret);
            $this->jsApiTicket = $ticket;
            $this->jsApiTime = time() + 7200;
        }
        return $ticket;
    }

    /****************************************************
     *  微信获取ApiTicket 返回指定微信公众号的at信息
     ****************************************************/
    /**
     * 获取JSAPI授权TICKET
     * @param string $appid 用于多个appid时使用,可空
     * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
     */
    public function getJsApiTicket()
    {
        // 手动指定token，优先使用
        if (!empty($this->jsapi_ticket)) {
            return $this->jsapi_ticket;
        }
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $cache_name = $this->cache_data_sign . $this->_appID;
        $cache_data = HelperCache::getCache($cache_name);
        if ($cache_data['jsapi_expire_time'] > time()) {
            return $cache_data['jsapi_ticket'];
        }

        $wxAccToken = $this->wxAccessToken();
        // if (!$this->access_token && !$this->wxAccessToken())
        //     return false;

        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$wxAccToken";

        $url = self::URL_API_PREFIX . "/ticket/getticket?type=jsapi&access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        if ($result) {
            $jsonInfo = json_decode($result, true);
            if (!$jsonInfo || !empty($jsonInfo['errcode'])) {
                $this->errCode = $jsonInfo['errcode'];
                $this->errMsg = $jsonInfo['errmsg'];
                return false;
            }
            if (!empty($jsonInfo['jsapi_ticket'])) {
                $data['jsapi_expire_time'] = time() + 7000;
                $data['jsapi_ticket'] = $jsonInfo['jsapi_ticket'];
                $expire = $jsonInfo['expires_in'] ? intval($jsonInfo['expires_in']) - 100 : 3600;
                HelperCache::setCache($cache_name, $data, $expire);
            } else {
                die('no ticket!');
            }
            return $jsonInfo['jsapi_ticket'];
        }
        return false;
    }
    // 直接优先设置
    public function setJsApiTicket($new_str)
    {
        $this->jsapi_ticket = $new_str;
        return $this;
    }
    /**
     * 删除JSAPI授权TICKET
     * @param string $appid 用于多个appid时使用
     */
    public function resetJsTicket($appid = '')
    {
        if (!$appid)
            $appid = $this->_appID;
        $this->jsapi_ticket = '';
        HelperCache::removeCache($this->cache_data_sign  . $appid);
        return true;
    }
}
