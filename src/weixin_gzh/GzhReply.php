<?php

namespace shiyunSdk\wechatGzh;

/**
 * 【ctocode】      微信 - 微信自动回复类
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v5.7.1.20210514
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 */
class GzhReply extends GzhCommon
{
    public function __construct($config = [])
    {
        parent::__construct($config);
        $echoStr = isset($_GET["echostr"]) && !empty($_GET["echostr"]) ? addslashes($_GET["echostr"]) : NULL;
        if (isset($echoStr)) {
            $this->valid($echoStr);
        } else {
            $this->responseMsg();
        }
    }
    // 响应消息
    public function responseMsg()
    {
        // 拿到数据后，可能是由于不同的环境
        $postStr = !empty($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");
        // 解压缩后的数据
        if (!empty($postStr)) {
            /*
			 * ibxml_disable_entity_loader是防止XML外部实体注入，最好的办法是检查 XML的自己有效性
			 */
            // libxml_disable_entity_loader ( true );
            // $postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
            // $fromUsername = $postObj->FromUserName;
            // $toUsername = $postObj->ToUserName;
            // $keyword = trim ( $postObj->Content );

            $this->logger("R " . $postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            $result = "";
            // 消息类型分离
            switch ($RX_TYPE) {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text": // 文本格式
                    $result = $this->receiveText($postObj);
                    break;
                case "image": // 图片格式
                    $result = $this->receiveImage($postObj);
                    break;
                case "location": // 上传地理位置
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice": // 声音
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video": // 视频
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link": // 链接相应
                    $result = $this->receiveLink($postObj);
                    break;
                case 'shortvideo': // 小视频
                    break;
                default:
                    $result = "unknown msg type: " . $RX_TYPE;
                    break;
            }
            $this->logger("T " . $result);
            echo $result;
        } else {
            echo "";
            exit();
        }
    }
    public function setReplyText($data = NULL)
    {
        $this->replyText = $data;
    }
    public function setReplyEvent($data = NULL)
    {
        $this->replyEvent = $data;
    }
    /* ============================================================== */
    // 接收事件消息
    private function receiveEvent($object)
    {
        // $uername=json_decode($object,true);
        // 当用户订阅后，需要存储下所有用户的信息，openid，昵称，地址等等；
        // 调用存储 函数，需要新创建。。。。
        $openID = $object->FromUserName;
        $EventKey = $object->EventKey;
        $content = "";
        switch ($object->Event) {
            case "subscribe": // 关注
                $diy_content = $this->replyEvent['subscribe'];
                if (!empty($diy_content)) {
                    $content = $diy_content;
                } else {
                    // $content = "欢迎您,关注公众号\n技术支持\n ctocode.com ";
                    $content = "欢迎您,关注公众号\n技术支持\n ctocode";
                }
                $content .= (!empty($EventKey)) ? ("\n来自二维码场景 " . str_replace("qrscene_", "", $EventKey)) : "";
                break;
            case "unsubscribe": // 取消关注
                $content = "取消关注";
                break;
            case "SCAN": // 扫描
                $content = "扫描场景 " . $object->EventKey;
                break;
            case "CLICK": // 点击事件
                switch ($object->EventKey) {
                    default:
                        $content = "你点击了菜单: " . $object->EventKey;
                        break;
                }
                break;
            case "LOCATION": // 地址
                $content = "上传位置：纬度 " . $object->Latitude . ";经度 " . $object->Longitude;
                break;
            case "VIEW": // 跳转
                $content = "跳转链接 " . $object->EventKey;
                break;
            case "MASSSENDJOBFINISH":
                $content = "消息ID：" . $object->MsgID . "，结果：" . $object->Status . "，粉丝数：" . $object->TotalCount . "，过滤：" . $object->FilterCount . "，发送成功：" . $object->SentCount . "，发送失败：" . $object->ErrorCount;
                break;
            case 'card_pass_check': // 卡券审核通过
                break;
            case 'card_not_pass_check': // 卡券审核失败
                break;
            case 'user_get_card': // 用户领取卡券
                break;
            case 'user_del_card': // 用户删除卡券
                break;
            case 'user_view_card': // 用户浏览会员卡
                break;
            case 'user_consume_card': // 用户核销卡券
                break;
            case 'kf_create_session': // 创建会话
                break;
            case 'kf_close_session': // 关闭会话
                break;
            case 'kf_switch_session': // 转接会话
                break;
            default:
                $content = "receive a new event: " . $object->Event;
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }
    /**
     * ==============================文本相关==============================
     */
    // 接收文本消息
    private function receiveText($object)
    {
        $reply_data = $this->replyText;

        $openID = $object->FromUserName;
        $keyword = trim($object->Content);
        // 多客服人工回复模式
        if (strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "在吗") || strstr($keyword, "客服")) {
            $result = $this->transmitService($object);
        } else {
            /*
			 * 自动回复模式
			 * 嵌套后台系统程序
			 */
            $reply_content = $reply_data[$keyword];
            if (strstr($keyword, "时间")) {
                $content = "当前时间为 --" . date("Y-m-d H:i:s", time());
            } elseif (strstr($keyword, "测试推送")) {
                $content = "http://api.10yun.com/wx/push?bid={$this->business_id}&openid=" . $openID;
            } elseif (!empty($reply_content)) {
                $content = $reply_content;
            } else {
                // $content = "欢迎您使用自助回复~ \n技术支持\nctocode.com ";
                $content = "欢迎您使用自助回复~ \n技术支持\n ctocode ";
                $result = $this->transmitService($object);
                return $result;
            }
            if (is_array($content)) {
                if (isset($content[0]['PicUrl'])) {
                    $result = $this->transmitNews($object, $content);
                } else if (isset($content['MusicUrl'])) {
                    $result = $this->transmitMusic($object, $content);
                }
            } else {
                $funcFlag = 0;
                $result = $this->transmitText($object, $content, $funcFlag);
            }
        }
        return $result;
    }
    // 回复文本消息
    private function transmitText($object, $content, $flag = 0)
    {
        $xmlTpl = $this->parseXmlTplData($object, 'text');
        $result = sprintf($xmlTpl, $content, $flag);
        return $result;
    }
    /**
     * ==============================图片相关==============================
     */
    private function receiveImage($object)
    {
        $content = array(
            "MediaId" => $object->MediaId
        );

        $result = $this->transmitImage($object, $content);
        return $result;
    }
    private function transmitImage($object, $content)
    {
        $xmlTpl = $this->parseXmlTplData($object, 'images');
        $result = sprintf($xmlTpl, $content['MediaId']);
        return $result;
    }
    /**
     * ==============================视频相关==============================
     */
    // 接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)) {
            $content = "你刚才说的是：" . $object->Recognition;
            $result = $this->transmitText($object, $content);
        } else {
            $content = array(
                "MediaId" => $object->MediaId
            );
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }

    // 回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $xmlTpl = $this->parseXmlTplData($object, 'voice');
        $result = sprintf($xmlTpl, $voiceArray['MediaId']);
        return $result;
    }

    /**
     * ==============================视频相关==============================
     */
    // 接收视频消息
    private function receiveVideo($object)
    {
        $content = array(
            "MediaId" => $object->MediaId,
            "ThumbMediaId" => $object->ThumbMediaId,
            "Title" => "",
            "Description" => ""
        );
        $result = $this->transmitVideo($object, $content);
        return $result;
    }
    // 回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $xmlTpl = $this->parseXmlTplData($object, 'video');
        $result = sprintf($xmlTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);
        return $result;
    }

    /**
     * ==============================图文相关==============================
     */
    // 回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if (!is_array($newsArray)) {
            return;
        }
        $itemTpl = "<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>";
        $item_str = "";
        foreach ($newsArray as $item) {
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = $this->parseXmlTplData($object, 'news');
        $result = sprintf($xmlTpl, count($newsArray), $item_str);
        return $result;
    }
    /**
     * ==============================音乐相关==============================
     */
    // 回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $xmlTpl = $this->parseXmlTplData($object, 'music');
        $result = sprintf($xmlTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
        return $result;
    }

    /**
     * ==============================客服相关==============================
     */

    // 回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = $this->parseXmlTplData($object, 'service');
        $result = $xmlTpl;
        return $result;
    }
    /**
     * ==============================位置相关==============================
     */
    // 接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：" . $object->Location_X . "；经度为：" . $object->Location_Y . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /**
     * ==============================链接相关==============================
     */
    // 接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    /**
     * ==============================解析模板==============================
     */
    private function parseXmlTplData($object, $type = '')
    {
        $fromUserName = $object->FromUserName; // 关注者的openID
        $toUserName = $object->ToUserName;
        $time = time();
        $xmlTpl = '';
        $xmlTpl .= "<xml>";
        $xmlTpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
        $xmlTpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
        $xmlTpl .= "<CreateTime>%s</CreateTime>";

        $xmlTpl = sprintf($xmlTpl, $fromUserName, $toUserName, $time);
        switch ($type) {
            case 'service':
                $xmlTpl .= "<MsgType><![CDATA[transfer_customer_service]]></MsgType>";
                break;
            case 'text':
                $xmlTpl .= "<MsgType><![CDATA[text]]></MsgType>";
                $xmlTpl .= "<Content><![CDATA[%s]]></Content>";
                // <FuncFlag>%d</FuncFlag>
                break;
            case 'images':
                $xmlTpl .= "<MsgType><![CDATA[image]]></MsgType>";
                $xmlTpl .= "<Image>";
                $xmlTpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $xmlTpl .= "</Image>";
                break;
            case 'video':
                $xmlTpl .= "<MsgType><![CDATA[video]]></MsgType>";
                $xmlTpl .= "<Video>";
                $xmlTpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $xmlTpl .= "<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>";
                $xmlTpl .= "<Title><![CDATA[%s]]></Title>";
                $xmlTpl .= "<Description><![CDATA[%s]]></Description>";
                $xmlTpl .= "</Video>";
                break;
            case 'music':
                $xmlTpl .= "<MsgType><![CDATA[music]]></MsgType>";
                $xmlTpl .= "<Music>";
                $xmlTpl .= "<Title><![CDATA[%s]]></Title>";
                $xmlTpl .= "<Description><![CDATA[%s]]></Description>";
                $xmlTpl .= "<MusicUrl><![CDATA[%s]]></MusicUrl>";
                $xmlTpl .= "<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>";
                $xmlTpl .= "</Music>";
                break;
            case 'voice':
                $xmlTpl .= "<MsgType><![CDATA[voice]]></MsgType>";
                $xmlTpl .= "<Voice>";
                $xmlTpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $xmlTpl .= "</Voice>";
                break;
            case 'news':
                $xmlTpl .= "<MsgType><![CDATA[news]]></MsgType>";
                $xmlTpl .= "<ArticleCount>%s</ArticleCount>";
                $xmlTpl .= "<Articles>%s</Articles>";
                break;
        }
        $xmlTpl .= "</xml>";
        return $xmlTpl;
    }
}
