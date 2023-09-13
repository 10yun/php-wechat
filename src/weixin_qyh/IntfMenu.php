<?php

namespace shiyunWechat\weixin_qyh;

use shiyunWechat\exception\WeixinException;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\WechatConst;

trait IntfMenu
{
    /**
     * 获取菜单
     */
    public function getMenu($agentid = '')
    {
        if ($agentid == '') {
            $agentid = $this->agentid;
        }
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/menu/get', [
            'access_token' => $wxAccToken,
            'agentid' => $agentid
        ]);
        return $result;
    }
    /**
     * 删除菜单
     */
    public function deleteMenu($agentid = '')
    {
        if ($agentid == '') {
            $agentid = $this->agentid;
        }
        $wxAccToken = $this->wxAccessToken();
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/menu/delete', [
            'access_token' => $wxAccToken,
            'agentid' => $agentid
        ]);
        return true;
        return $result;
    }
}
