<?php

namespace shiyunSdk\wechatSdk\libs;

/**
 * 微信通用 helper 常用方法
 *
 */
class HelperStr
{
    // 常用工具
    public static function trimString($value)
    {
        $ret = null;
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }
    /**
     * 生成随机字符串 - 不长于32位
     * 生成随机字符串 - 最长为32位字符串
     * @param number $length 长度，默认为16，最长为32字节
     * @return string
     */
    public static function createNoncestr($length = 16, $type = false)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            // $str .= $chars[mt_rand(0, strlen($chars) - 1)];
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        if ($type == TRUE) {
            return strtoupper(md5(time() . $str));
        } else {
            return $str;
        }
    }
    /**
     * 	作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            // $buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}
