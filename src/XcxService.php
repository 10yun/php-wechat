<?php

namespace shiyunSdk\wechatSdk;

/**.
 * Author: ctocode-zhw
 * Date: 2019/2/26
 */
class XcxService extends WechatCommon
{
    public function xcxDoGetData($wxConfig, $code = '', $encryptedData = '', $iv = '')
    {
        if (empty($wxConfig)) {
            return array(
                'status' => 404,
                'msg' => "配置信息 错误"
            );
        }
        if (empty($code)) {
            return array(
                'status' => 404,
                'msg' => "code 错误"
            );
        }
        // var_dump($wxConfig, $code , $encryptedData, $iv);

        $XcxResultArr = $this->xcxGetBaseParam($wxConfig['appid'], $wxConfig['appsecret'], $code);
        // 获取用户基本信息
        if (!empty($encryptedData) && !empty($iv) && !empty($XcxResultArr['session_key'])) {
            $XcxResultArr1 = $this->xcxGetUserInfo($wxConfig['appid'], $XcxResultArr['session_key'], $encryptedData, $iv);
            if ($XcxResultArr1['status'] == 200) {
                $XcxResultArr = array_merge($XcxResultArr, $XcxResultArr1);
                // return array(
                // 'status' => 404,
                // 'msg' => "揭秘失败，请重试～",
                // 'info' => $XcxResultArr
                // );
            }
            if (!empty($XcxResultArr['openId'])) {
                $XcxResultArr['openid'] = $XcxResultArr['openId'];
            }
            if (!empty($XcxResultArr['unionId'])) {
                $XcxResultArr['unionid'] = $XcxResultArr['unionId'];
            }
        }
        if (empty($XcxResultArr['openid'])) {
            return array(
                'status' => 404,
                'msg' => "openid 为空，可能code已经消耗",
                'info' => $XcxResultArr
            );
        }
        return array_merge(array(
            'status' => 200
        ), $XcxResultArr);
    }
    /**
     *  开发者使用登陆凭证 code 
     *  获取 
     *  session_key、openid、unionid
     */
    public function xcxGetBaseParam($APPID, $AppSecret, $code)
    {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
    /**
     *  获取用户 信息
     */
    //
    public function xcxGetUserInfo($APPID = '', $session_key = '', $encryptedData = '', $iv = '')
    {
        include __DIR__ . '/XcxBizDataCrypt.php';
        $pc = new XcxBizDataCrypt($APPID, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data); // 其中$data包含用户的所有数据
        if ($errCode == 0) {
            $data = json_decode($data, true);
            $data['status'] = 200;
            return $data;
        }
        return array(
            'status' => 500,
            'msg' => $errCode
        );
    }
    /**
     * 小程序生成二维码
     * @param string $page 已经发布的小程序存在的页面,如：pages/index/index
     * @param string $scene 场景值，用于存参数
     * @return mixed
     */
    public function getWXACodeUnlimit($page, $scene = '')
    {
        $data = [];
        $data['page'] = $page;
        $data['scene'] = $scene ?: 'default';
        $jsonData = json_encode($data);
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $wxAccessToken;
        return $this->wxHttpsRequest($url, $jsonData);
    }
}
