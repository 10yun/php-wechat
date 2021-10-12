<?php

namespace shiyunSdk\wechatSdk;

use think\facade\Cache;

use shiyunSdk\wechatSdk\common\TraitBaseHelper;
use shiyunSdk\wechatSdk\common\TraitWxCurl;
use shiyunSdk\wechatSdk\common\TraitWxCache;

/**
 * App优化 
 */
class WxJssdk extends WechatCommon
{
    use TraitBaseHelper, TraitWxCurl, TraitWxCache;
    private $appId;
    private $appSecret;
    private $path;
    private $token; // access_token
    private $url;
    private $cacheType = 'file'; // 缓存类型
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->path = __DIR__ . DS;
    }
    public function construct2($options = [])
    {
        $this->appId = isset($options['appId']) ? $options['appId'] : '';
        $this->token = isset($options['token']) ? $options['token'] : '';
        $this->url = isset($options['url']) ? $options['url'] : '';
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

    /**
     * 获取JSAPI授权TICKET
     * @param string $appid 用于多个appid时使用,可空
     * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
     */
    private function getJsApiTicket($accessToken = '')
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = $this->getJsApiData();
        if ($data['jsapi_expire_time'] < time()) {
            if (empty($accessToken)) {
                $accessToken = $this->wxAccessToken();
            }
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";

            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            if ($res->errcode == 0) {
                // $ticket = $res->ticket;
            }
            $ticket = $res->ticket;
            if (!empty($ticket)) {
                $data['jsapi_expire_time'] = time() + 7000;
                $data['jsapi_ticket'] = $ticket;
                $this->saveJsApiData($data);
            } else {
                die('no ticket!');
            }
        } else {
            $ticket = $data['jsapi_ticket'];
        }
        return $ticket;
    }
    public function getJsApiTicket2($appid = '', $jsapi_ticket = '')
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
    private function getJsApiData()
    {
        switch ($this->cacheType) {
            case 'cache':
                $data = Cache::get('jsapi_ticket');
                break;
            case 'curl':
                $cacheData = ctoHttpCurl(_URL_API_ . "wx/opt", array(
                    'type' => 'getSet',
                    'wx_id' => _TOOL_WX_SETT_ID_
                ));
                $data = json_decode($cacheData, true)['data'];
                break;
            case 'file':
                // ==== 文件存储方式
                $cacheData = trim(substr(file_get_contents(__DIR__ . '/jsapi_ticket.php'), 15));
                $data = json_decode($cacheData);
                break;
            default:;
                break;
        }

        return $data;
    }
    private function saveJsApiData($data)
    {
        switch ($this->cacheType) {
            case 'cache':
                Cache::set('jsapi_ticket', $data, 110); // jsapi_ticket有效期2小时，提前10分钟获取
                break;
            case 'curl':
                ctoHttpCurl(_URL_API_ . "wx/opt", array(
                    'type' => 'updateSet',
                    'wx_id' => _TOOL_WX_SETT_ID_,
                    'data' => $data
                ));
            case 'file':
                // ==== 文件存储方式
                $this->set_php_file("jsapi_ticket.php", json_encode($data));
                break;

            default:;
                break;
        }
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
        $authname = 'wechat_jsapi_ticket' . $appid;
        $this->removeCache($authname);
        return true;
    }
    private function set_php_file($filename, $content)
    {
        // file_put_contents("./Data/jsapi_ticket.json", json_encode($data));
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        // fwrite($fp, json_encode($data));
        fclose($fp);
    }
}
