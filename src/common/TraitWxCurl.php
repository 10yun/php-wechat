<?php

namespace shiyunSdk\wechatSdk\common;

/**
 * 微信通用 curl 常用方法
 *
 */
trait TraitWxCurl
{

    /*
     * curl 请求
     */
    public function get_curl_json($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print_r(curl_error($ch));
        }
        curl_close($ch);
        return json_decode($result, true);
    }

    // 执行第三方接口或取数据
    public function postUrl($url, $data)
    {
        $guzzHttp = new \GuzzleHttp\Client();
        // $response = $http->get($apiUrl);
        // $result = json_decode($response->getBody(), true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // 设置发送数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        // 设置url
        curl_setopt($curl, CURLOPT_URL, $postUrl);
        // 设置发送方式：post
        curl_setopt($curl, CURLOPT_POST, true);


        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        // return $result;
        return analysJsonDecode($result, true);
    }



    public function curlHttpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // true或者1
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        // 无证书
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1); // CURL_SSLVERSION_TLSv1 或者1
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        $aStatus = curl_getinfo($curl);
        curl_close($curl);
        if (intval($aStatus["http_code"]) == 200) {
            return $res;
        } else {
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    public function curlHttpPost($url, $param, $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
}
