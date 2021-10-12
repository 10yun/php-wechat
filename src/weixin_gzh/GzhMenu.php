<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\libs\HelperCurl;

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
    /****************************************************
     *  创建自定义菜单
     ****************************************************/
    public function wxMenuCreate($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/menu/create?access_token=" . $wxAccToken;
        $result = HelperCurl::wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  获取自定义菜单
     ****************************************************/
    public function wxMenuGet()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/menu/get?access_token=" . $wxAccToken;
        $result = HelperCurl::wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  删除自定义菜单
     ****************************************************/
    public function wxMenuDelete()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/menu/delete?access_token=" . $wxAccToken;
        $result = HelperCurl::wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *  获取第三方自定义菜单
     ****************************************************/
    public function wxMenuGetInfo()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/get_current_selfmenu_info?access_token=" . $wxAccToken;
        $result = HelperCurl::wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }


    const EVENT_MENU_VIEW = 'VIEW'; // 菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK'; // 菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push'; // 菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; // 菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto'; // 菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album'; // 菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin'; // 菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select'; // 菜单 - 弹出地理位置选择器
    /**
     * 创建菜单(认证后的订阅号可用)
     * @param array $data 菜单数组数据
     * example:
     *    array (
     *        'button' => array (
     *          0 => array (
     *            'name' => '扫码',
     *            'sub_button' => array (
     *                0 => array (
     *                  'type' => 'scancode_waitmsg',
     *                  'name' => '扫码带提示',
     *                  'key' => 'rselfmenu_0_0',
     *                ),
     *                1 => array (
     *                  'type' => 'scancode_push',
     *                  'name' => '扫码推事件',
     *                  'key' => 'rselfmenu_0_1',
     *                ),
     *            ),
     *          ),
     *          1 => array (
     *            'name' => '发图',
     *            'sub_button' => array (
     *                0 => array (
     *                  'type' => 'pic_sysphoto',
     *                  'name' => '系统拍照发图',
     *                  'key' => 'rselfmenu_1_0',
     *                ),
     *                1 => array (
     *                  'type' => 'pic_photo_or_album',
     *                  'name' => '拍照或者相册发图',
     *                  'key' => 'rselfmenu_1_1',
     *                )
     *            ),
     *          ),
     *          2 => array (
     *            'type' => 'location_select',
     *            'name' => '发送位置',
     *            'key' => 'rselfmenu_2_0'
     *          ),
     *        ),
     *    )
     * type可以选择为以下几种，会收到相应类型的事件推送。请注意，3到8的所有事件，仅支持微信iPhone5.4.1以上版本，
     * 和Android5.4以上版本的微信用户，旧版本微信用户点击后将没有回应，开发者也不能正常接收到事件推送。
     * type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
     * 1、click：点击推事件
     * 2、view：跳转URL
     * 3、scancode_push：扫码推事件
     * 4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
     * 5、pic_sysphoto：弹出系统拍照发图
     * 6、pic_photo_or_album：弹出拍照或者相册发图
     * 7、pic_weixin：弹出微信相册发图器
     * 8、location_select：弹出地理位置选择器
     */
    public function createMenu($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/menu/create?access_token=' . $this->access_token,
            self::json_encode($data)
        );
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
    public function createMenu2($data, $agentid = '')
    {
        if ($agentid == '') {
            $agentid = $this->agentid;
        }
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/menu/create?access_token=' . $this->access_token . '&agentid=' . $agentid,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
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
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/menu/get?access_token=' . $this->access_token
        );
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
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/menu/get?access_token=' . $this->access_token . '&agentid=' . $agentid
        );
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
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX . '/menu/delete?access_token=' . $this->access_token
        );
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
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX . '/menu/delete?access_token=' . $this->access_token . '&agentid=' . $agentid
        );
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
