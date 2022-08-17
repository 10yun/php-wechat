<?php

namespace shiyunWechat\common;


trait TraitBaseRev
{
    private $_receive;
    /**
     * 获取微信服务器发来的信息
     */
    public function getRev()
    {
        if ($this->_receive)
            return $this;
        $postStr = !empty($this->postxml) ? $this->postxml : file_get_contents("php://input");
        // 兼顾使用明文又不想调用valid()方法的情况
        $this->log($postStr);
        if (!empty($postStr)) {
            $this->_receive = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        return $this;
    }
    /**
     * 获取微信服务器发来的信息
     */
    public function getRevData()
    {
        return $this->_receive;
    }
    /**
     * 获取消息发送者
     */
    public function getRevFrom()
    {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }
    /**
     * 获取消息接受者
     */
    public function getRevTo()
    {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }
    /**
     * 获取接收消息的类型
     */
    public function getRevType()
    {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }
    /**
     * 获取消息ID
     */
    public function getRevID()
    {
        if (isset($this->_receive['MsgId']))
            return $this->_receive['MsgId'];
        else
            return false;
    }
    /**
     * 获取消息发送时间
     */
    public function getRevCtime()
    {
        if (isset($this->_receive['CreateTime']))
            return $this->_receive['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent()
    {
        if (isset($this->_receive['Content']))
            return $this->_receive['Content'];
        else if (isset($this->_receive['Recognition'])) // 获取语音识别文字内容，需申请开通
            return $this->_receive['Recognition'];
        else
            return false;
    }

    /**
     * 获取接收消息图片
     */
    public function getRevPic()
    {
        if (isset($this->_receive['PicUrl']))
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'picurl' => (string) $this->_receive['PicUrl'] // 防止picurl为空导致解析出错
            );
        else
            return false;
    }

    /**
     * 获取接收消息链接
     */
    public function getRevLink()
    {
        if (isset($this->_receive['Url'])) {
            return array(
                'url' => $this->_receive['Url'],
                'title' => $this->_receive['Title'],
                'description' => $this->_receive['Description']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     */
    public function getRevGeo()
    {
        if (isset($this->_receive['Location_X'])) {
            return array(
                'x' => $this->_receive['Location_X'],
                'y' => $this->_receive['Location_Y'],
                'scale' => $this->_receive['Scale'],
                'label' => $this->_receive['Label']
            );
        } else
            return false;
    }

    /**
     * 获取上报地理位置事件
     */
    public function getRevEventGeo()
    {
        if (isset($this->_receive['Latitude'])) {
            return array(
                'x' => $this->_receive['Latitude'],
                'y' => $this->_receive['Longitude'],
                'precision' => $this->_receive['Precision']
            );
        } else
            return false;
    }

    /**
     * 获取接收事件推送
     */
    public function getRevEvent()
    {
        if (isset($this->_receive['Event'])) {
            $array['event'] = $this->_receive['Event'];
        }
        if (isset($this->_receive['EventKey'])) {
            $array['key'] = $this->_receive['EventKey'];
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的扫码推事件信息
     *
     * 事件类型为以下两种时则调用此方法有效
     * Event     事件类型，scancode_push
     * Event     事件类型，scancode_waitmsg
     *
     * @return: array | false
     * array (
     *     'ScanType'=>'qrcode',
     *     'ScanResult'=>'123123'
     * )
     */
    public function getRevScanInfo()
    {
        if (isset($this->_receive['ScanCodeInfo'])) {
            if (!is_array($this->_receive['ScanCodeInfo'])) {
                $array = (array) $this->_receive['ScanCodeInfo'];
                $this->_receive['ScanCodeInfo'] = $array;
            } else {
                $array = $this->_receive['ScanCodeInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的图片发送事件信息
     *
     * 事件类型为以下三种时则调用此方法有效
     * Event     事件类型，pic_sysphoto        弹出系统拍照发图的事件推送
     * Event     事件类型，pic_photo_or_album  弹出拍照或者相册发图的事件推送
     * Event     事件类型，pic_weixin          弹出微信相册发图器的事件推送
     *
     * @return: array | false
     * array (
     *   'Count' => '2',
     *   'PicList' =>array (
     *         'item' =>array (
     *             0 =>array ('PicMd5Sum' => 'aaae42617cf2a14342d96005af53624c'),
     *             1 =>array ('PicMd5Sum' => '149bd39e296860a2adc2f1bb81616ff8'),
     *         ),
     *   ),
     * )
     *
     */
    public function getRevSendPicsInfo()
    {
        if (isset($this->_receive['SendPicsInfo'])) {
            if (!is_array($this->_receive['SendPicsInfo'])) {
                $array = (array) $this->_receive['SendPicsInfo'];
                if (isset($array['PicList'])) {
                    $array['PicList'] = (array) $array['PicList'];
                    $item = $array['PicList']['item'];
                    $array['PicList']['item'] = array();
                    foreach ($item as $key => $value) {
                        $array['PicList']['item'][$key] = (array) $value;
                    }
                }
                $this->_receive['SendPicsInfo'] = $array;
            } else {
                $array = $this->_receive['SendPicsInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的地理位置选择器事件推送
     *
     * 事件类型为以下时则可以调用此方法有效
     * Event     事件类型，location_select        弹出地理位置选择器的事件推送
     *
     * @return: array | false
     * array (
     *   'Location_X' => '33.731655000061',
     *   'Location_Y' => '113.29955200008047',
     *   'Scale' => '16',
     *   'Label' => '某某市某某区某某路',
     *   'Poiname' => '',
     * )
     *
     */
    public function getRevSendGeoInfo()
    {
        if (isset($this->_receive['SendLocationInfo'])) {
            if (!is_array($this->_receive['SendLocationInfo'])) {
                $array = (array) $this->_receive['SendLocationInfo'];
                if (empty($array['Poiname'])) {
                    $array['Poiname'] = "";
                }
                if (empty($array['Label'])) {
                    $array['Label'] = "";
                }
                $this->_receive['SendLocationInfo'] = $array;
            } else {
                $array = $this->_receive['SendLocationInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取接收语音推送
     */
    public function getRevVoice()
    {
        if (isset($this->_receive['MediaId'])) {
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'format' => $this->_receive['Format']
            );
        } else
            return false;
    }

    /**
     * 获取接收视频推送
     */
    public function getRevVideo()
    {
        if (isset($this->_receive['MediaId'])) {
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'thumbmediaid' => $this->_receive['ThumbMediaId']
            );
        } else
            return false;
    }


    /**
     * 获取接收TICKET
     */
    public function getRevTicket()
    {
        if (isset($this->_receive['Ticket'])) {
            return $this->_receive['Ticket'];
        } else
            return false;
    }

    /**
     * 获取二维码的场景值
     */
    public function getRevSceneId()
    {
        if (isset($this->_receive['EventKey'])) {
            return str_replace('qrscene_', '', $this->_receive['EventKey']);
        } else {
            return false;
        }
    }

    /**
     * 获取主动推送的消息ID
     * 经过验证，这个和普通的消息MsgId不一样
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH
     */
    public function getRevTplMsgID()
    {
        if (isset($this->_receive['MsgID'])) {
            return $this->_receive['MsgID'];
        } else
            return false;
    }

    /**
     * 获取模板消息发送状态
     */
    public function getRevStatus()
    {
        if (isset($this->_receive['Status'])) {
            return $this->_receive['Status'];
        } else
            return false;
    }

    /**
     * 获取群发或模板消息发送结果
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH，即高级群发/模板消息
     */
    public function getRevResult()
    {
        if (isset($this->_receive['Status'])) // 发送是否成功，具体的返回值请参考 高级群发/模板消息 的事件推送说明
            $array['Status'] = $this->_receive['Status'];
        if (isset($this->_receive['MsgID'])) // 发送的消息id
            $array['MsgID'] = $this->_receive['MsgID'];

        // 以下仅当群发消息时才会有的事件内容
        if (isset($this->_receive['TotalCount'])) // 分组或openid列表内粉丝数量
            $array['TotalCount'] = $this->_receive['TotalCount'];
        if (isset($this->_receive['FilterCount'])) // 过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数
            $array['FilterCount'] = $this->_receive['FilterCount'];
        if (isset($this->_receive['SentCount'])) // 发送成功的粉丝数
            $array['SentCount'] = $this->_receive['SentCount'];
        if (isset($this->_receive['ErrorCount'])) // 发送失败的粉丝数
            $array['ErrorCount'] = $this->_receive['ErrorCount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取多客服会话状态推送事件 - 接入会话
     * 当Event为 kfcreatesession 即接入会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFCreate()
    {
        if (isset($this->_receive['KfAccount'])) {
            return $this->_receive['KfAccount'];
        } else
            return false;
    }

    /**
     * 获取多客服会话状态推送事件 - 关闭会话
     * 当Event为 kfclosesession 即关闭会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFClose()
    {
        if (isset($this->_receive['KfAccount'])) {
            return $this->_receive['KfAccount'];
        } else
            return false;
    }

    /**
     * 获取多客服会话状态推送事件 - 转接会话
     * 当Event为 kfswitchsession 即转接会话
     * @return array | boolean  返回分配到的客服
     * {
     *     'FromKfAccount' => '',      //原接入客服
     *     'ToKfAccount' => ''            //转接到客服
     * }
     */
    public function getRevKFSwitch()
    {
        if (isset($this->_receive['FromKfAccount'])) // 原接入客服
            $array['FromKfAccount'] = $this->_receive['FromKfAccount'];
        if (isset($this->_receive['ToKfAccount'])) // 转接到客服
            $array['ToKfAccount'] = $this->_receive['ToKfAccount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 卡卷审核是否通过
     * 当Event为 card_pass_check(审核通过) 或 card_not_pass_check(未通过)
     * @return string|boolean  返回卡券ID
     */
    public function getRevCardPass()
    {
        if (isset($this->_receive['CardId']))
            return $this->_receive['CardId'];
        else
            return false;
    }

    /**
     * 获取卡券事件推送 - 领取卡券
     * 当Event为 user_get_card(用户领取卡券)
     * @return array|boolean
     */
    public function getRevCardGet()
    {
        if (isset($this->_receive['CardId'])) // 卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['IsGiveByFriend'])) // 是否为转赠，1 代表是，0 代表否。
            $array['IsGiveByFriend'] = $this->_receive['IsGiveByFriend'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) // code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 删除卡券
     * 当Event为 user_del_card(用户删除卡券)
     * @return array|boolean
     */
    public function getRevCardDel()
    {
        if (isset($this->_receive['CardId'])) // 卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) // code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }
}
