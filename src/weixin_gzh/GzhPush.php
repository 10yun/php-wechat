<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\libs\HelperCurl;

/**
 * 【ctocode】      微信 - 消息接收和推送等功能类
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 */
class GzhPush extends GzhCommon
{
    /****************************************************
     *  微信通过指定模板信息发送给指定用户，发送完成后返回指定JSON数据
     ****************************************************/
    public function wxSendTemplate($jsonData)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/message/template/send?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        return $result;
    }

    /****************************************************
     *      发送自定义的模板消息
     ****************************************************/
    public function wxSetSend($touser, $template_id, $url = '', $data, $topcolor = '#7B68EE')
    {
        $template = array(
            'touser' => $touser,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $data
        );
        $jsonData = urldecode(json_encode($template));
        $result = $this->wxSendTemplate($jsonData);
        return $result;
    }
    /*
	 * 设置公众号所属行业【每月可更改1次所选行业】否则报错：change template too frequently
	 * access_token 应当由上层应用,通过调用本类中的wxAccessToken来获取,上层应用要对access_token 做存储有效性判断处理.
	 * $data = array(1,2);最多只能设置两个行业;
	 */
    public function wxTemplateIndustrySet(array $data)
    {
        $jsonData = json_encode($data);

        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/template/api_set_industry?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        return $result;
    }
    /****************************************************
     *      设置模板ID
     ****************************************************/
    public function wxTemplateAdd($template_id_short)
    {
        if (empty($template_id_short)) {
            return NULL;
        }
        $data = array(
            'template_id_short' => $template_id_short
        );
        $jsonData = json_encode($data);
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/template/api_add_template?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    /****************************************************
     *      获取模板列表
     ****************************************************/
    public function wxTemplateGet($access_token)
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/template/get_all_private_template?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
    /****************************************************
     *      删除模板
     ****************************************************/
    public function wxTemplateDelete($template_id)
    {
        if (empty($template_id)) {
            return NULL;
        }
        $data = array(
            'template_id' => $template_id
        );
        $jsonData = json_encode($data);
        $url = self::URL_API_PREFIX . "/template/del_private_template?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpPost($url, $jsonData);
        return $result;
    }

    /****************************************************
     *      获取设置的行业信息
     ****************************************************/
    public function wxTemplateGetIndustry()
    {
        $wxAccToken = $this->wxAccessToken();
        $url = self::URL_API_PREFIX . "/template/get_industry?access_token={$wxAccToken}";
        $result = HelperCurl::curlHttpGet($url);
        return $result;
    }
}
