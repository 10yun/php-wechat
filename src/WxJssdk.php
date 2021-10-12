<?php

namespace shiyunSdk\wechatSdk;

use think\facade\Cache;

use shiyunSdk\wechatSdk\common\TraitBaseHelper;
use shiyunSdk\wechatSdk\common\TraitWxCurl;

/**
 * App优化 
 */
class WxJssdk
{
    use TraitBaseHelper, TraitWxCurl;
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
            case 'file':
                // ==== 文件存储方式
                $this->set_php_file("jsapi_ticket.php", json_encode($data));
                break;
            case 'curl':
                ctoHttpCurl(_URL_API_ . "wx/opt", array(
                    'type' => 'updateSet',
                    'wx_id' => _TOOL_WX_SETT_ID_,
                    'data' => $data
                ));
            default:;
                break;
        }
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
