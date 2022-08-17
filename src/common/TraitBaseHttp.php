<?php

namespace shiyunWechat\common;

trait TraitBaseHttp
{
    /**
     * GET 请求
     * @param string $url
     */
    public function curlHttpGet($url)
    {
        $chObj = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($chObj, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        }
        curl_setopt($chObj, CURLOPT_URL, $url);
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($chObj);
        $aStatus = curl_getinfo($chObj);
        curl_close($chObj);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
    private function curlHttpGet2($url)
    { // 模拟获取内容函数
        $header = array(
            'Accept: */*',
            'Connection: keep-alive',
            'Host: mp.weixin.qq.com',
            'Referer: ' . $this->referer,
            'X-Requested-With: XMLHttpRequest'
        );

        $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0';
        $chObj = curl_init(); // 启动一个CURL会话
        curl_setopt($chObj, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($chObj, CURLOPT_HTTPHEADER, $header); // 设置HTTP头字段的数组
        curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($chObj, CURLOPT_USERAGENT, $useragent); // 模拟用户使用的浏览器
        curl_setopt($chObj, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($chObj, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($chObj, CURLOPT_HTTPGET, 1); // 发送一个常规的GET请求
        curl_setopt($chObj, CURLOPT_COOKIE, $this->cookie); // 读取上面所储存的Cookie信息
        curl_setopt($chObj, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($chObj, CURLOPT_HEADER, $this->getHeader); // 显示返回的Header区域内容
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($chObj); // 执行操作
        if (curl_errno($chObj)) {
            // echo 'Errno'.curl_error($chObj);
        }
        curl_close($chObj); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

    /**
     * curl模拟登录的post方法
     * @param $url string request地址
     * @param $header array 模拟headre头信息
     * @return array json
     */
    private function curlHttpPost2($url, $sendData)
    {
        $header = array(
            'Accept:*/*',
            'Accept-Charset:GBK,utf-8;q=0.7,*;q=0.3',
            'Accept-Encoding:gzip,deflate,sdch',
            'Accept-Language:zh-CN,zh;q=0.8',
            'Connection:keep-alive',
            'Host:' . $this->host,
            'Origin:' . $this->origin,
            'Referer:' . $this->referer,
            'X-Requested-With:XMLHttpRequest'
        );
        $chObj = curl_init(); // 启动一个curl会话
        curl_setopt($chObj, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($chObj, CURLOPT_HTTPHEADER, $header); // 设置HTTP头字段的数组
        curl_setopt($chObj, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($chObj, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($chObj, CURLOPT_USERAGENT, $this->useragent); // 模拟用户使用的浏览器
        curl_setopt($chObj, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($chObj, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($chObj, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($chObj, CURLOPT_POSTFIELDS, $sendData); // Post提交的数据包
        curl_setopt($chObj, CURLOPT_COOKIE, $this->cookie); // 读取储存的Cookie信息
        curl_setopt($chObj, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($chObj, CURLOPT_HEADER, $this->getHeader); // 显示返回的Header区域内容
        curl_setopt($chObj, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($chObj); // 执行一个curl会话
        curl_close($chObj); // 关闭curl
        return $result;
    }
}
