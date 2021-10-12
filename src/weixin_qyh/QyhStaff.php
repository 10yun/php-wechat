<?php

namespace shiyunSdk\wechatQyh;

use shiyunSdk\wechatQyh\QyhBase;
use shiyunSdk\wechatSdk\libs\HelperCurl;

class GzhQyStaff extends QyhBase
{
    /**
     * 创建成员
     * @param array $data 	结构体为:
     * array(
     *    "userid" => "zhangsan",
     *    "name" => "张三",
     *    "department" => [1, 2],
     *    "position" => "产品经理",
     *    "mobile" => "15913215421",
     *    "gender" => 1,     //性别。gender=0表示男，=1表示女
     *    "tel" => "62394",
     *    "email" => "zhangsan@gzdev.com",
     *    "weixinid" => "zhangsan4dev"
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "created",  //对返回码的文本描述内容
     * }
     */
    public function createUser($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX .  '/user/create?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 更新成员
     * @param array $data 	结构体为:
     * array(
     *    "userid" => "zhangsan",
     *    "name" => "张三",
     *    "department" => [1, 2],
     *    "position" => "产品经理",
     *    "mobile" => "15913215421",
     *    "gender" => 1,     //性别。gender=0表示男，=1表示女
     *    "tel" => "62394",
     *    "email" => "zhangsan@gzdev.com",
     *    "weixinid" => "zhangsan4dev"
     * )
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "updated"  //对返回码的文本描述内容
     * }
     */
    public function updateUser($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/user/update?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除成员
     * @param $userid  员工UserID。对应管理端的帐号
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "deleted"  //对返回码的文本描述内容
     * }
     */
    public function deleteUser($userid)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/user/delete?access_token=' . $this->access_token . '&userid=' . $userid
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取成员信息
     * @param $userid  员工UserID。对应管理端的帐号
     * @return boolean|array	 成功返回结果
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "userid": "zhangsan",
     *    "name": "李四",
     *    "department": [1, 2],
     *    "position": "后台工程师",
     *    "mobile": "15913215421",
     *    "gender": 1,     //性别。gender=0表示男，=1表示女
     *    "tel": "62394",
     *    "email": "zhangsan@gzdev.com",
     *    "weixinid": "lisifordev",        //微信号
     *    "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3W..../0",   //头像url。注：如果要获取小图将url最后的"/0"改成"/64"即可
     *    "status": 1      //关注状态: 1=已关注，2=已冻结，4=未关注
     * }
     */
    public function getUserInfo($userid)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX . '/user/get?access_token=' . $this->access_token . '&userid=' . $userid
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取部门成员
     * @param $department_id   部门id
     * @param $fetch_child     1/0：是否递归获取子部门下面的成员
     * @param $status          0获取全部员工，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加
     * @return boolean|array	 成功返回结果
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "userlist": [
     *            {
     *                   "userid": "zhangsan",
     *                   "name": "李四"
     *            }
     *      ]
     * }
     */
    public function getUserList($department_id, $fetch_child = 0, $status = 0)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/user/simplelist?access_token=' . $this->access_token . '&department_id=' . $department_id . '&fetch_child=' . $fetch_child . '&status=' . $status
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 根据code获取成员信息
     * 通过Oauth2.0或者设置了二次验证时获取的code，用于换取成员的UserId和DeviceId
     *
     * @param $code        Oauth2.0或者二次验证时返回的code值
     * @param $agentid     跳转链接时所在的企业应用ID，未填则默认为当前配置的应用id
     * @return boolean|array 成功返回数组
     * array(
     *     'UserId' => 'USERID',       //员工UserID
     *     'DeviceId' => 'DEVICEID'    //手机设备号(由微信在安装时随机生成)
     * )
     */
    public function getUserId($code, $agentid = 0)
    {
        if (!$agentid)
            $agentid = $this->agentid;
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/user/getuserinfo?access_token=' . $this->access_token . '&code=' . $code . '&agentid' . $agentid
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode']) || $json['errcode'] != 0) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
}
