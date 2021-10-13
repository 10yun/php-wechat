<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\libs\HelperCurl;

class GzhPushTemplate extends GzhBase
{

    /**
     * 模板消息 设置所属行业
     * @param int $id1 公众号模板消息所属行业编号，参看官方开发文档 行业代码
     * @param int $id2 同$id1。但如果只有一个行业，此参数可省略
     * @return boolean|array
     */
    public function setTMIndustry($id1, $id2 = '')
    {
        if ($id1)
            $data['industry_id1'] = $id1;
        if ($id2)
            $data['industry_id2'] = $id2;
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX . "/message/template/api_set_industry?access_token={$this->access_token}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));

        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 模板消息 添加消息模板
     * 成功返回消息模板的调用id
     * @param string $tpl_id 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return boolean|string
     */
    public function addTemplateMessage($tpl_id)
    {
        $data = array(
            'template_id_short' => $tpl_id
        );
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX . "/message/template/api_add_template?access_token={$this->access_token}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));

        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['template_id'];
        }
        return false;
    }

    /**
     * 发送模板消息
     * @param array $data 消息结构
     * ｛
     * "touser":"OPENID",
     * "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
     * "url":"http://weixin.qq.com/download",
     * "topcolor":"#FF0000",
     * "data":{
     * "参数名1": {
     * "value":"参数",
     * "color":"#173177"     //参数颜色
     * },
     * "Date":{
     * "value":"06月07日 19时24分",
     * "color":"#173177"
     * },
     * "CardNumber":{
     * "value":"0426",
     * "color":"#173177"
     * },
     * "Type":{
     * "value":"消费",
     * "color":"#173177"
     * }
     * }
     * }
     * @return boolean|array
     */
    public function sendTemplateMessage($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;

        $url = self::URL_API_PREFIX . "/message/template/send?access_token={$this->access_token}";
        $result = HelperCurl::curlHttpPost($url, self::json_encode($data));


        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
}
