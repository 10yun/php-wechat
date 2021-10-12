<?php

namespace shiyunSdk\wechatGzh;

/**
 * 【ctocode】      微信 - 客服类、
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
class GzhService extends GzhCommon
{
    /****************************************************
     *  微信客服接口 - Add 添加客服人员
     ****************************************************/
    public function wxServiceAdd($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  微信客服接口 - Update 编辑客服人员
     ****************************************************/
    public function wxServiceUpdate($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfaccount/update?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  微信客服接口 - Delete 删除客服人员
     ****************************************************/
    public function wxServiceDelete($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfaccount/del?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信客服接口 - 上传头像
     *******************************************************/
    public function wxServiceUpdateCover($kf_account, $media = '')
    {
        $wxAccessToken = $this->wxAccessToken();
        // $data['access_token'] = $wxAccessToken;
        $data['media'] = '@D:\\workspace\\htdocs\\yky_test\\logo.jpg';
        $url = "https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=" . $wxAccessToken . "&kf_account=" . $kf_account;
        $result = $this->wxHttpsRequest($url, $data);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服接口 - 获取客服列表
     ****************************************************/
    public function wxServiceList()
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/customservice/getkflist?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服接口 - 获取在线客服接待信息
     ****************************************************/
    public function wxServiceOnlineList()
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/customservice/getonlinekflist?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服接口 - 客服发送信息
     ****************************************************/
    public function wxServiceSend($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/message/custom/send?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 创建会话
     ****************************************************/
    public function wxServiceSessionAdd($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfsession/create?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 关闭会话
     ****************************************************/
    public function wxServiceSessionClose()
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfsession/close?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 获取会话
     ****************************************************/
    public function wxServiceSessionGet($openId)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfsession/getsession?access_token=" . $wxAccessToken . "&openid=" . $openId;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 获取会话列表
     ****************************************************/
    public function wxServiceSessionList($kf_account)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfsession/getsessionlist?access_token=" . $wxAccessToken . "&kf_account=" . $kf_account;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信客服会话接口 - 未接入会话
     ****************************************************/
    public function wxServiceSessionWaitCase()
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfsession/getwaitcase?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
}
