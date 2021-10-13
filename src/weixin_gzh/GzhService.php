<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\libs\HelperCurl;

/**
 * 【ctocode】      微信 - 客服类、
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */
class GzhService extends GzhCommon
{
    /****************************************************
     *  微信客服接口 - Add 添加客服人员
     ****************************************************/
    public function wxServiceAdd($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfaccount/add?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  微信客服接口 - Update 编辑客服人员
     ****************************************************/
    public function wxServiceUpdate($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfaccount/update?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  微信客服接口 - Delete 删除客服人员
     ****************************************************/
    public function wxServiceDelete($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfaccount/del?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信客服接口 - 上传头像
     *******************************************************/
    public function wxServiceUpdateCover($kf_account, $media = '')
    {
        $wxAccToken = $this->wxAccessToken();
        // $data['access_token'] = $wxAccToken;
        $data['media'] = '@D:\\workspace\\htdocs\\yky_test\\logo.jpg';
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfaccount/uploadheadimg?access_token={$wxAccToken}&kf_account=" . $kf_account;
        $result = HelperCurl::curlHttpPost($url, $data);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服接口 - 获取客服列表
     ****************************************************/
    public function wxServiceList()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/customservice/getkflist?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服接口 - 获取在线客服接待信息
     ****************************************************/
    public function wxServiceOnlineList()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/customservice/getonlinekflist?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服接口 - 客服发送信息
     ****************************************************/
    public function wxServiceSend($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/message/custom/send?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 创建会话
     ****************************************************/
    public function wxServiceSessionAdd($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfsession/create?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 关闭会话
     ****************************************************/
    public function wxServiceSessionClose()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfsession/close?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 获取会话
     ****************************************************/
    public function wxServiceSessionGet($openId)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfsession/getsession?access_token={$wxAccToken}&openid=" . $openId;
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 获取会话列表
     ****************************************************/
    public function wxServiceSessionList($kf_account)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfsession/getsessionlist?access_token={$wxAccToken}&kf_account=" . $kf_account;
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 未接入会话
     ****************************************************/
    public function wxServiceSessionWaitCase()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/customservice/kfsession/getwaitcase?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
}
