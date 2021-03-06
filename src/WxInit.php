<?php

namespace shiyunSdk\wechatSdk;

use shiyunSdk\wechatSdk\common\TraitWxLog;

class WxInit extends WechatCommon
{
    use TraitWxLog;

    public $debug = false;
    public $logcallback;
    protected $_logcallback;

    public function __construct($options)
    {
        $this->debug = isset($options['debug']) ? $options['debug'] : false;
        $this->_logcallback = isset($options['logcallback']) ? $options['logcallback'] : false;
    }
}
