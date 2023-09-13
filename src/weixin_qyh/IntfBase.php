<?php

namespace shiyunWechat\weixin_qyh;

use shiyunWechat\exception\WeixinException;
use shiyunWechat\libs\HelperCurl;
use shiyunWechat\WechatConst;

trait IntfBase
{
    /**
     * 企业号 获取 access_token
     */
    public function wxAccessToken()
    {
        $result = HelperCurl::curlHttpParamGet(WechatConst::URL_API_CGI_PREFIX . '/gettoken', [
            'corpid' => $this->_appID,
            'corpsecret' => $this->_appSecret,
        ]);
        $accessToken = '';
        return $accessToken;
    }
}
