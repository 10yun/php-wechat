<?php

namespace shiyunSdk\wechatSdk;

use shiyunSdk\wechatSdk\common\TraitWxLog;
use shiyunSdk\wechatSdk\common\TraitWxToken;

class WxInit extends WechatCommon
{
    use TraitWxLog, TraitWxToken;

    public $debug = false;
    public $logcallback;
    protected $_logcallback;

    public function __construct($options)
    {
        $this->debug = isset($options['debug']) ? $options['debug'] : false;
        $this->_logcallback = isset($options['logcallback']) ? $options['logcallback'] : false;
    }
}
