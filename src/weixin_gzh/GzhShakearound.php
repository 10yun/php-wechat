<?php

namespace shiyunSdk\wechatGzh;

/**
 * 【ctocode】      微信 - 摇一摇类、
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */
class GzhShakearound extends GzhCommon
{
    /****************************************************
     *      微信摇一摇 - 申请设备ID
     ****************************************************/
    public function wxDeviceApply($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/device/applyid?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 编辑设备ID
     ****************************************************/
    public function wxDeviceUpdate($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/device/update?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 本店关联设备
     ****************************************************/
    public function wxDeviceBindLocation($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/device/bindlocation?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 查询设备列表
     ****************************************************/
    public function wxDeviceSearch($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/device/search?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 新增页面
     ****************************************************/
    public function wxPageAdd($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/page/add?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 编辑页面
     ****************************************************/
    public function wxPageUpdate($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/page/update?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 查询页面
     ****************************************************/
    public function wxPageSearch($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/page/search?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 删除页面
     ****************************************************/
    public function wxPageDelete($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/page/delete?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /*******************************************************
     *      微信摇一摇 - 上传图片素材
     *******************************************************/
    public function wxMaterialAdd($media = '')
    {
        $wxAccessToken = $this->wxAccessToken();
        // $data['access_token'] = $wxAccessToken;
        $data['media'] = '@D:\\workspace\\htdocs\\yky_test\\logo.jpg';
        $url = "https://api.weixin.qq.com/shakearound/material/add?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $data);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 配置设备与页面的关联关系
     ****************************************************/
    public function wxDeviceBindPage($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/device/bindpage?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 获取摇周边的设备及用户信息
     ****************************************************/
    public function wxGetShakeInfo($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/user/getshakeinfo?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      微信摇一摇 - 以设备为维度的数据统计接口
     ****************************************************/
    public function wxGetShakeStatistics($jsonData)
    {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://api.weixin.qq.com/shakearound/statistics/device?access_token=" . $wxAccessToken;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
}
