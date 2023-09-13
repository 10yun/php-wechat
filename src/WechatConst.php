<?php

namespace shiyunWechat;

class WechatConst
{
    // 以下API接口URL需要使用此前缀
    /**
     * api
     */
    const URL_API_BASE_PREFIX = 'https://api.weixin.qq.com';
    const URL_API_CGI_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    /**
     * 公众号
     */
    const URL_MP_BASE_PREFIX = 'https://mp.weixin.qq.com';
    const URL_MP_CGI_PREFIX = 'https://mp.weixin.qq.com/cgi-bin';

    const URL_OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const URL_UPLOAD_MEDIA = 'http://file.api.weixin.qq.com/cgi-bin';
    /**
     * 企业微信
     */
    const URL_QY_CGI_PREFIX = 'https://qyapi.weixin.qq.com/cgi-bin';
}
