<?php

namespace shiyunSdk\wechatSdk;

class WxOauth extends WxInit
{

    /**
     * 刷新access token并续期
     * @param string $refresh_token
     * @return boolean|mixed
     */
    public function getOauthRefreshToken($refresh_token)
    {
        $result = $this->curlHttpGet(
            'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='
                . $this->_appID . '&grant_type=refresh_token&refresh_token=' . $refresh_token
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function getOauthUserinfo($access_token, $openid)
    {
        $result = $this->curlHttpGet(
            'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid
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
     * 检验授权凭证是否有效
     * @param string $access_token
     * @param string $openid
     * @return boolean 是否有效
     */
    public function getOauthAuth($access_token, $openid)
    {
        $result = $this->curlHttpGet(
            'https://api.weixin.qq.com/sns/auth?access_token=' . $access_token . '&openid=' . $openid
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            } else if ($json['errcode'] == 0)
                return true;
        }
        return false;
    }


    /****************************************************
     *  微信设置OAUTH跳转URL，返回字符串信息 - SCOPE = snsapi_base //验证时不返回确认页面，只能获取OPENID
     ****************************************************/
    /**
     * oauth 授权跳转接口
     * @param string $redirectUrl 回调URI
     * @param string $state 重定向后会带上state参数，企业可以填写a-zA-Z0-9的参数值
     * @return string
     */
    public function wxOauthRedirectBase($redirectUrl, $state = "", $appId = NULL)
    {
        $appId = is_null($appId) ? $this->_appID : $appId;
        // $redirectUrl =  urlencode($redirectUrl) ;
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . $redirectUrl . "&response_type=code&scope=snsapi_base&state=" . $state . "#wechat_redirect";
        return $url;
    }


    /****************************************************
     *  微信设置OAUTH跳转URL，返回字符串信息 - SCOPE = snsapi_userinfo //获取用户完整信息
     ****************************************************/
    public function wxOauthRedirectUserinfo($redirectUrl, $state = "", $appId = null)
    {
        $appId = is_null($appId) ? $this->_appID : $appId;
        $redirectUrl =  urlencode($redirectUrl);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . $redirectUrl . "&response_type=code&scope=snsapi_userinfo&state=" . $state . "#wechat_redirect";
        return $url;
    }

    /****************************************************
     *  微信OAUTH跳转指定URL
     ****************************************************/
    public function wxHeader($url)
    {
        header("location:" . $url);
    }
    /****************************************************
     *  微信通过OAUTH返回页面中获取AT信息
     ****************************************************/
    public function wxOauthAccessToken($code, $appId = null, $appSecret = null)
    {
        $appId = is_null($appId) ? $this->_appID : $appId;
        $appSecret = is_null($appSecret) ? $this->_appSecret : $appSecret;

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid="
            . $appId
            . "&secret=" . $appSecret
            . "&code=" . $code
            . "&grant_type=authorization_code";

        $result = $this->wxHttpsRequest($url);
        // print_r($result);
        $jsoninfo = json_decode($result, true);
        // $access_token = $jsoninfo["access_token"];
        return $jsoninfo;
    }
    /**
     * 通过code获取Access Token
     * @return array {access_token,expires_in,refresh_token,openid,scope}
     */
    public function wxOauthAccessToken2()
    {
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        if (!$code)
            return false;
        $result = $this->curlHttpGet(
            'https://api.weixin.qq.com/sns/oauth2/access_token?appid='
                . $this->_appID
                . '&secret=' . $this->appsecret
                . '&code=' . $code
                . '&grant_type=authorization_code'
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }


    /****************************************************
     *  微信通过OAUTH的Access_Token的信息获取当前用户信息 // 只执行在snsapi_userinfo模式运行
     ****************************************************/
    public function wxOauthUser($OauthAT, $openId)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $OauthAT . "&openid=" . $openId . "&lang=zh_CN";
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
}
