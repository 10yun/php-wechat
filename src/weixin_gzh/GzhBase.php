<?php

/**
 *    微信公众平台PHP-SDK, 官方API部分
 * @author  dodge <dodgepudding@gmail.com>
 * @link https://github.com/dodgepudding/wechat-php-sdk
 * @version 1.2
 **/

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechat\WxInit;

class GzhBase extends WxInit
{
    const URL_API_BASE_PREFIX = 'https://api.weixin.qq.com'; // 以下API接口URL需要使用此前缀
    const URL_API_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const URL_MP_PREFIX  = 'https://mp.weixin.qq.com/cgi-bin/';
    const URL_OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const URL_UPLOAD_MEDIA = 'http://file.api.weixin.qq.com/cgi-bin';

    const QR_SCENE = 0;
    const QR_LIMIT_SCENE = 1;
    // /多客服相关地址    
    private $token;
    private $encodingAesKey;
    private $encrypt_type;
    private $_appID;
    private $appsecret;
    private $access_token;
    private $jsapi_ticket;
    private $user_token;
    private $postxml;
    private $_msg;
    public $errCode = 40001;
    public $errMsg = "no access";

    public function __construct($options)
    {
        $this->token = isset($options['token']) ? $options['token'] : '';
        $this->encodingAesKey = isset($options['encodingaeskey']) ? $options['encodingaeskey'] : '';
        $this->_appID = isset($options['appid']) ? $options['appid'] : '';
        $this->appsecret = isset($options['appsecret']) ? $options['appsecret'] : '';
    }

    /**
     * For weixin server validation
     */
    private function checkSignature($str = '')
    {
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
        $signature = isset($_GET["msg_signature"]) ? $_GET["msg_signature"] : $signature; // 如果存在加密验证则用加密验证段
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';

        $token = $this->token;
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce,
            $str
        );
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * For weixin server validation
     * @param bool $return 是否返回
     */
    public function valid($return = false)
    {
        $encryptStr = "";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $postStr = file_get_contents("php://input");
            $array = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->encrypt_type = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"] : '';
            if ($this->encrypt_type == 'aes') { // aes加密
                $this->log($postStr);
                $encryptStr = $array['Encrypt'];
                $pc = new Prpcrypt($this->encodingAesKey);
                $array = $pc->decrypt($encryptStr, $this->_appID);
                if (!isset($array[0]) || ($array[0] != 0)) {
                    if (!$return) {
                        die('decrypt error!');
                    } else {
                        return false;
                    }
                }
                $this->postxml = $array[1];
                if (!$this->_appID)
                    $this->_appID = $array[2]; // 为了没有_appID的订阅号。
            } else {
                $this->postxml = $postStr;
            }
        } elseif (isset($_GET["echostr"])) {
            $echoStr = $_GET["echostr"];
            if ($return) {
                if ($this->checkSignature())
                    return $echoStr;
                else
                    return false;
            } else {
                if ($this->checkSignature())
                    die($echoStr);
                else
                    die('no access');
            }
        }

        if (!$this->checkSignature($encryptStr)) {
            if ($return)
                return false;
            else
                die('no access');
        }
        return true;
    }

    /**
     * 设置发送消息
     * @param array $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     */
    public function Message($msg = '', $append = false)
    {
        if (is_null($msg)) {
            $this->_msg = array();
        } elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg, $msg);
            else
                $this->_msg = $msg;
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }
    /**
     * 获取access_token
     * @param string $appid 如在类初始化时已提供，则可为空
     * @param string $appsecret 如在类初始化时已提供，则可为空
     * @param string $token 手动指定access_token，非必要情况不建议用
     */
    public function checkAuth($appid = '', $appsecret = '', $token = '')
    {
        if (!$appid || !$appsecret) {
            $appid = $this->_appID;
            $appsecret = $this->appsecret;
        }
        if ($token) { // 手动指定token，优先使用
            $this->access_token = $token;
            return $this->access_token;
        }

        $authname = 'wechat_access_token' . $appid;
        if ($rs = $this->getCache($authname)) {
            $this->access_token = $rs;
            return $rs;
        }

        $result = $this->curlHttpGet(
            'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->access_token = $json['access_token'];
            $expire = $json['expires_in'] ? intval($json['expires_in']) - 100 : 3600;
            $this->setCache($authname, $this->access_token, $expire);
            return $this->access_token;
        }
        return false;
    }

    /**
     * 删除验证数据
     * @param string $appid
     */
    public function resetAuth($appid = '')
    {
        if (!$appid)
            $appid = $this->_appID;
        $this->access_token = '';
        $authname = 'wechat_access_token' . $appid;
        $this->removeCache($authname);
        return true;
    }


    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    static function json_encode($arr)
    {
        $parts = array();
        $is_list = false;
        // Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] === 0) && ($keys[$max_length] === $max_length)) { // See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { // See if each key correspondes to its position
                if ($i != $keys[$i]) { // A key fails at position check.
                    $is_list = false; // It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { // Custom handling for arrays
                if ($is_list)
                    $parts[] = self::json_encode($value); /* :RECURSION: */
                else
                    $parts[] = '"' . $key . '":' . self::json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                // Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; // Numbers
                elseif ($value === false)
                    $str .= 'false'; // The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; // All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; // Return numerical JSON
        return '{' . $json . '}'; // Return associative JSON
    }


    /**
     * 获取微信服务器IP地址列表
     * @return array('127.0.0.1','127.0.0.1')
     */
    public function getServerIp()
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->curlHttpGet(
            'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $this->access_token
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['ip_list'];
        }
        return false;
    }


    /**
     * 上传视频素材(认证后的订阅号可用)
     * @param array $data 消息结构
     * {
     *     "media_id"=>"",     //通过上传媒体接口得到的MediaId
     *     "title"=>"TITLE",    //视频标题
     *     "description"=>"Description"        //视频描述
     * }
     * @return boolean|array
     * {
     *     "type":"video",
     *     "media_id":"mediaid",
     *     "created_at":1398848981
     *  }
     */
    public function uploadMpVideo($data)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->curlHttpPost(
            self::URL_UPLOAD_MEDIA .  '/media/uploadvideo?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }


    /**
     * 创建二维码ticket
     * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
     * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
     * @param int $expire 临时二维码有效期，最大为1800秒
     * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800,'url'=>'二维码图片解析后的地址')
     */
    public function getQRCode($scene_id, $type = 0, $expire = 1800)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $type = ($type && is_string($scene_id)) ? 2 : $type;
        $data = array(
            'action_name' => $type ? ($type == 2 ? "QR_LIMIT_STR_SCENE" : "QR_LIMIT_SCENE") : "QR_SCENE",
            'expire_seconds' => $expire,
            'action_info' => array(
                'scene' => ($type == 2 ? array(
                    'scene_str' => $scene_id
                ) : array(
                    'scene_id' => $scene_id
                ))
            )
        );
        if ($type == 1) {
            unset($data['expire_seconds']);
        }
        $result = $this->curlHttpPost(
            'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回http地址
     */
    public function getQRUrl($ticket)
    {
        return self::URL_MP_PREFIX . 'showqrcode?ticket=' . urlencode($ticket);
    }

    /**
     * 长链接转短链接接口
     * @param string $long_url 传入要转换的长url
     * @return boolean|string url 成功则返回转换后的短url
     */
    public function getShortUrl($long_url)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'action' => 'long2short',
            'long_url' => $long_url
        );
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX . 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['short_url'];
        }
        return false;
    }

    /**
     * 获取统计数据
     * @param string $type 数据分类(user|article|upstreammsg|interface)分别为(用户分析|图文分析|消息分析|接口分析)
     * @param string $subtype 数据子分类，参考 DATACUBE_URL_ARR 常量定义部分 或者README.md说明文档
     * @param string $begin_date 开始时间
     * @param string $end_date 结束时间
     * @return boolean|array 成功返回查询结果数组，其定义请看官方文档
     */
    public function getDatacube($type, $subtype, $begin_date, $end_date = '')
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;

        // /数据分析接口
        $DATACUBE_URL_ARR = array( // 用户分析
            'user' => array(
                'summary' => '/datacube/getusersummary?', // 获取用户增减数据（getusersummary）
                'cumulate' => '/datacube/getusercumulate?' // 获取累计用户数据（getusercumulate）
            ),
            'article' => array( // 图文分析
                'summary' => '/datacube/getarticlesummary?', // 获取图文群发每日数据（getarticlesummary）
                'total' => '/datacube/getarticletotal?', // 获取图文群发总数据（getarticletotal）
                'read' => '/datacube/getuserread?', // 获取图文统计数据（getuserread）
                'readhour' => '/datacube/getuserreadhour?', // 获取图文统计分时数据（getuserreadhour）
                'share' => '/datacube/getusershare?', // 获取图文分享转发数据（getusershare）
                'sharehour' => '/datacube/getusersharehour?' // 获取图文分享转发分时数据（getusersharehour）
            ),
            'upstreammsg' => array( // 消息分析
                'summary' => '/datacube/getupstreammsg?', // 获取消息发送概况数据（getupstreammsg）
                'hour' => '/datacube/getupstreammsghour?', // 获取消息分送分时数据（getupstreammsghour）
                'week' => '/datacube/getupstreammsgweek?', // 获取消息发送周数据（getupstreammsgweek）
                'month' => '/datacube/getupstreammsgmonth?', // 获取消息发送月数据（getupstreammsgmonth）
                'dist' => '/datacube/getupstreammsgdist?', // 获取消息发送分布数据（getupstreammsgdist）
                'distweek' => '/datacube/getupstreammsgdistweek?', // 获取消息发送分布周数据（getupstreammsgdistweek）
                'distmonth' => '/datacube/getupstreammsgdistmonth?' // 获取消息发送分布月数据（getupstreammsgdistmonth）
            ),
            'interface' => array( // 接口分析
                'summary' => '/datacube/getinterfacesummary?', // 获取接口分析数据（getinterfacesummary）
                'summaryhour' => '/datacube/getinterfacesummaryhour?' // 获取接口分析分时数据（getinterfacesummaryhour）
            )
        );

        if (!isset($DATACUBE_URL_ARR[$type]) || !isset($DATACUBE_URL_ARR[$type][$subtype]))
            return false;
        $data = array(
            'begin_date' => $begin_date,
            'end_date' => $end_date ? $end_date : $begin_date
        );
        $result = $this->curlHttpPost(
            self::URL_API_BASE_PREFIX . $DATACUBE_URL_ARR[$type][$subtype] . 'access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return isset($json['list']) ? $json['list'] : $json;
        }
        return false;
    }


    /**
     * 语义理解接口
     * @param String $uid 用户唯一id（非开发者id），用户区分公众号下的不同用户（建议填入用户openid）
     * @param String $query 输入文本串
     * @param String $category 需要使用的服务类型，多个用“，”隔开，不能为空
     * @param Float $latitude 纬度坐标，与经度同时传入；与城市二选一传入
     * @param Float $longitude 经度坐标，与纬度同时传入；与城市二选一传入
     * @param String $city 城市名称，与经纬度二选一传入
     * @param String $region 区域名称，在城市存在的情况下可省略；与经纬度二选一传入
     * @return boolean|array
     */
    public function querySemantic($uid, $query, $category, $latitude = 0, $longitude = 0, $city = "", $region = "")
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'query' => $query,
            'category' => $category,
            'appid' => $this->_appID,
            'uid' => ''
        );
        // 地理坐标或城市名称二选一
        if ($latitude) {
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;
        } elseif ($city) {
            $data['city'] = $city;
        } elseif ($region) {
            $data['region'] = $region;
        }
        $result = $this->curlHttpPost(
            self::URL_API_BASE_PREFIX .  '/semantic/semproxy/search?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
}
