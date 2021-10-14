<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\WechatCommon;
use shiyunSdk\wechatSdk\libs\HelperCurl;

/**
 * 【ctocode】      微信 - 卡券类
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */
include_once __DIR__ . '/WechatCommon.php';
class WechatCard extends WechatCommon
{
    /*******************************************************
     *      微信卡券：JSAPI 卡券Package - 基础参数没有附带任何值 - 再生产环境中需要根据实际情况进行修改
     *******************************************************/
    public function wxCardPackage($cardId, $timestamp = '')
    {
        $api_ticket = $this->wxVerifyJsApiTicket();
        if (!empty($timestamp)) {
            $timestamp = $timestamp;
        } else {
            $timestamp = time();
        }
        $arrays = array(
            $this->_appSecret,
            $timestamp,
            $cardId
        );
        sort($arrays, SORT_STRING);
        // print_r($arrays);
        // echo implode("",$arrays)."<br >";
        $string = sha1(implode($arrays));
        // echo $string;
        $resultArray['cardId'] = $cardId;
        $resultArray['cardExt'] = array();
        $resultArray['cardExt']['code'] = '';
        $resultArray['cardExt']['openid'] = '';
        $resultArray['cardExt']['timestamp'] = $timestamp;
        $resultArray['cardExt']['signature'] = $string;
        // print_r($resultArray);
        return $resultArray;
    }

    /*******************************************************
     *      微信卡券：JSAPI 卡券全部卡券 Package
     *******************************************************/
    public function wxCardAllPackage($cardIdArray = array(), $timestamp = '')
    {
        $reArrays = array();
        if (!empty($cardIdArray) && (is_array($cardIdArray) || is_object($cardIdArray))) {
            // print_r($cardIdArray);
            foreach ($cardIdArray as $value) {
                // print_r($this->wxCardPackage($value,$openid));
                $reArrays[] = $this->wxCardPackage($value, $timestamp);
            }
            // print_r($reArrays);
        } else {
            $reArrays[] = $this->wxCardPackage($cardIdArray, $timestamp);
        }
        return strval(json_encode($reArrays));
    }
    /*******************************************************
     *      微信卡券：获取卡券列表
     *******************************************************/
    public function wxCardListPackage($cardType = "", $cardId = "")
    {
        // $api_ticket = $this->wxVerifyJsApiTicket();
        $resultArray = array();
        $timestamp = time();
        $nonceStr = HelperRandom::doNumLetter(16);
        // $strings =
        $arrays = array(
            $this->_appID,
            $this->_appSecret,
            $timestamp,
            $nonceStr
        );
        sort($arrays, SORT_STRING);
        $string = sha1(implode($arrays));

        $resultArray['app_id'] = $this->_appID;
        $resultArray['card_sign'] = $string;
        $resultArray['time_stamp'] = $timestamp;
        $resultArray['nonce_str'] = $nonceStr;
        $resultArray['card_type'] = $cardType;
        $resultArray['card_id'] = $cardId;
        return $resultArray;
    }
    /*******************************************************
     *      微信卡券：上传LOGO - 需要改写动态功能
     *******************************************************/
    public function wxCardUpdateImg()
    {
        $wxAccToken = $this->wxAccessToken();
        // $data['access_token'] = $wxAccToken;
        $data['buffer'] = '@D:\\workspace\\htdocs\\yky_test\\logo.jpg';
        $url = self::URL_API_PREFIX . "/media/uploadimg?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $data);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
        // array(1) { ["url"]=> string(121) "http://mmbiz.qpic.cn/mmbiz/ibuYxPHqeXePNTW4ATKyias1Cf3zTKiars9PFPzF1k5icvXD7xW0kXUAxHDzkEPd9micCMCN0dcTJfW6Tnm93MiaAfRQ/0" }
    }

    /*******************************************************
     *      微信卡券：获取颜色
     *******************************************************/
    public function wxCardColor()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/getcolors?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：拉取门店列表
     *******************************************************/
    public function wxBatchGet($offset = 0, $count = 0)
    {
        $jsonData = json_encode(array(
            'offset' => intval($offset),
            'count' => intval($count)
        ));
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/location/batchget?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：创建卡券
     *******************************************************/
    public function wxCardCreated($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/create?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：查询卡券详情
     *******************************************************/
    public function wxCardGetInfo($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/get?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：设置白名单
     *******************************************************/
    public function wxCardWhiteList($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/testwhitelist/set?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：消耗卡券
     *******************************************************/
    public function wxCardConsume($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/code/consume?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：删除卡券
     *******************************************************/
    public function wxCardDelete($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/delete?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：选择卡券 - 解析CODE
     *******************************************************/
    public function wxCardDecryptCode($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/code/decrypt?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：更改库存
     *******************************************************/
    public function wxCardModifyStock($cardId, $increase_stock_value = 0, $reduce_stock_value = 0)
    {
        if (intval($increase_stock_value) == 0 && intval($reduce_stock_value) == 0) {
            return false;
        }

        $jsonData = json_encode(array(
            "card_id" => $cardId,
            'increase_stock_value' => intval($increase_stock_value),
            'reduce_stock_value' => intval($reduce_stock_value)
        ));

        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/modifystock?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信卡券：查询用户CODE
     *******************************************************/
    public function wxCardQueryCode($code, $cardId = '')
    {
        $jsonData = json_encode(array(
            "code" => $code,
            'card_id' => $cardId
        ));

        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_BASE_PREFIX . "/card/code/get?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
}
