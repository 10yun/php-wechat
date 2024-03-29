<?php

namespace shiyunWechat\exception;

use Exception;

/**
 * 公众号 异常处理类
 */
class GzhException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        // 可以自定义异常类的构造函数
        parent::__construct($message, $code, $previous);
    }
}
