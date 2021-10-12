<?php

namespace shiyunSdk\wechatSdk;

use think\facade\Cache;

use shiyunSdk\wechatSdk\libs\HelperCurl;
use shiyunSdk\wechatSdk\libs\HelperStr;
use shiyunSdk\wechatSdk\libs\HelperCache;

/**
 * App优化 
 */
class WxJssdk extends WechatCommon
{
    private $_appID;
    private $_appSecret;
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
    public function wxJsApiPackage()
    {
        $jsapi_ticket = $this->wxVerifyJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

        $timestamp = time();
        $nonceStr = HelperStr::createNoncestr(16);

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
    public function wxVerifyJsApiTicket($appId = NULL, $appSecret = NULL)
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
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = HelperCache::getCache('wechat_jsapi_ticket' . $this->_appID);
        if ($data['jsapi_expire_time'] > time()) {
            $ticket = $data['jsapi_ticket'];
            return $ticket;
        }
        if (empty($wxAccToken)) {
            $wxAccToken = $this->wxAccessToken();
        }
        $wxAccToken = $this->wxAccessToken();

        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$wxAccToken";

        $result = HelperCurl::wxHttpsRequest(
            self::URL_API_PREFIX . "/ticket/getticket?type=jsapi&access_token={$wxAccToken}"
        );
        $jsonInfo = json_decode($result, true);
        $ticket = $jsonInfo['jsapi_ticket'];
        if (!empty($ticket)) {
            $data['jsapi_expire_time'] = time() + 7000;
            $data['jsapi_ticket'] = $ticket;
            HelperCache::setCache($data);
        } else {
            die('no ticket!');
        }
        return $ticket;
    }
    // 直接优先设置
    public function setJsApiTicket($new_str)
    {
        $this->jsapi_ticket = $new_str;
        return $this;
    }
    public function getJsApiTicket3($appid = '')
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        if (!$appid)
            $appid = $this->_appID;

        // 手动指定token，优先使用
        if (!empty($this->jsapi_ticket)) {
            return $this->jsapi_ticket;
        }
        $cache_name = 'wechat_jsapi_ticket' . $appid;
        if ($rs = HelperCache::getCache()) {
            $this->jsapi_ticket = $rs;
            return $rs;
        }
        $result = HelperCurl::curlHttpGet(
            '/ticket/getticket?access_token=' . $this->access_token . '&type=jsapi'
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
            HelperCache::setCache($cache_name, $this->jsapi_ticket, $expire);
            return $this->jsapi_ticket;
        }
        return false;
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
        HelperCache::removeCache('wechat_jsapi_ticket' . $appid);
        return true;
    }
}
