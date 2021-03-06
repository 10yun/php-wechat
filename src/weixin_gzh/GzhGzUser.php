<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\libs\HelperCurl;

/**
 * 关注
 */
class GzhGzUser extends GzhBase
{

    /**
     * 批量获取关注用户列表
     * @param unknown $next_openid
     */
    public function getUserList($next_openid = '')
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX . "/user/get?access_token={$this->access_token}" . '&next_openid=' . $next_openid;
        $result = HelperCurl::curlHttpGet($url);
        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取关注者详细信息
     * @param string $openid
     * @return array {subscribe,openid,nickname,sex,city,province,country,language,headimgurl,subscribe_time,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function getUserInfo($openid)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX . "/user/info?access_token={$this->access_token}" . '&openid=' . $openid;
        $result = HelperCurl::curlHttpGet($url);

        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 设置用户备注名
     * @param string $openid
     * @param string $remark 备注名
     * @return boolean|array
     */
    public function updateUserRemark($openid, $remark)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $data = array(
            'openid' => $openid,
            'remark' => $remark
        );

        $url = self::URL_API_PREFIX . "/user/info/updateremark?access_token={$this->access_token}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));


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
