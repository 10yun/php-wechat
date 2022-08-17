<?php

namespace shiyunSdk\weixin_gzh;

use shiyunWechat\libs\HelperCurl;

/**
 * 【ctocode】      微信 - 菜单类
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */
class GzhMenu extends GzhCommon
{

    const EVENT_MENU_VIEW = 'VIEW'; // 菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK'; // 菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push'; // 菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; // 菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto'; // 菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album'; // 菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin'; // 菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select'; // 菜单 - 弹出地理位置选择器

    /****************************************************
     *  创建自定义菜单
     ****************************************************/
    public function wxMenuCreate($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/menu/create?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  获取自定义菜单
     ****************************************************/
    public function wxMenuGet()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/menu/get?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  删除自定义菜单
     ****************************************************/
    public function wxMenuDelete()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/menu/delete?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  获取第三方自定义菜单
     ****************************************************/
    public function wxMenuGetInfo()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/get_current_selfmenu_info?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }


    public function createMenu($data, $agentid = '')
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;


        $url = self::URL_API_PREFIX . '/menu/create?access_token=' . $this->access_token;

        // if ($agentid == '') {
        //     $agentid = $this->agentid;
        // }
        // if (!empty($agentid)) {
        //     $url = self::URL_API_PREFIX . '/menu/create?access_token=' . $this->access_token . '&agentid=' . $agentid;
        // }
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            // if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取菜单(认证后的订阅号可用)
     * @return array('menu'=>array(....s))
     */
    public function getMenu()
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX .  '/menu/get?access_token=' . $this->access_token;
        $result = HelperCurl::curlHttpGet($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    public function getMenu2($agentid = '')
    {
        if ($agentid == '') {
            $agentid = $this->agentid;
        }
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $url = self::URL_API_PREFIX .  '/menu/get?access_token=' . $this->access_token . '&agentid=' . $agentid;
        $result = HelperCurl::curlHttpGet($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode']) || $json['errcode'] != 0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除菜单(认证后的订阅号可用)
     * @return boolean
     */
    public function deleteMenu()
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX . '/menu/delete?access_token=' . $this->access_token;
        $result = HelperCurl::curlHttpGet($url);

        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    public function deleteMenu2($agentid = '')
    {
        if ($agentid == '') {
            $agentid = $this->agentid;
        }
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX . '/menu/delete?access_token=' . $this->access_token . '&agentid=' . $agentid;
        $result = HelperCurl::curlHttpGet($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
}
