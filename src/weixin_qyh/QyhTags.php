<?php

namespace shiyunSdk\wechatQyh;

use shiyunSdk\wechatQyh\QyhBase;
use shiyunSdk\wechatSdk\libs\HelperCurl;

class GzhTags extends QyhBase
{

    /**
     * 创建标签
     * @param array $data 	结构体为:
     * array(
     *    "tagname" => "UI"
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "created",  //对返回码的文本描述内容
     *   "tagid": "1"
     * }
     */
    public function createTag($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX .  '/tag/create?access_token=' . $this->access_token,
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
     * 更新标签
     * @param array $data 	结构体为:
     * array(
     *    "tagid" => "1",
     *    "tagname" => "UI design"
     * )
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "updated"  //对返回码的文本描述内容
     * }
     */
    public function updateTag($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/tag/update?access_token=' . $this->access_token,
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
     * 删除标签
     * @param $tagid  标签TagID
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "deleted"  //对返回码的文本描述内容
     * }
     */
    public function deleteTag($tagid)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/tag/delete?access_token=' . $this->access_token . '&tagid=' . $tagid
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
     * 获取标签成员
     * @param $tagid  标签TagID
     * @return boolean|array	 成功返回结果
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "userlist": [
     *          {
     *              "userid": "zhangsan",
     *              "name": "李四"
     *          }
     *      ]
     * }
     */
    public function getTag($tagid)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX . '/tag/get?access_token=' . $this->access_token . '&tagid=' . $tagid
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
     * 增加标签成员
     * @param array $data 	结构体为:
     * array (
     *    "tagid" => "1",
     *    "userlist" => array(    //企业员工ID列表
     *         "user1",
     *         "user2"
     *     )
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "ok",  //对返回码的文本描述内容
     *   "invalidlist"："usr1|usr2|usr"     //若部分userid非法，则会有此段。不在权限内的员工ID列表，以“|”分隔
     * }
     */
    public function addTagUser($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/tag/addtagusers?access_token=' . $this->access_token,
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
     * 删除标签成员
     * @param array $data 	结构体为:
     * array (
     *    "tagid" => "1",
     *    "userlist" => array(    //企业员工ID列表
     *         "user1",
     *         "user2"
     *     )
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "deleted",  //对返回码的文本描述内容
     *   "invalidlist"："usr1|usr2|usr"     //若部分userid非法，则会有此段。不在权限内的员工ID列表，以“|”分隔
     * }
     */
    public function delTagUser($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/tag/deltagusers?access_token=' . $this->access_token,
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
     * 获取标签列表
     * @return boolean|array	 成功返回数组结果，这里附上json样例
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "taglist":[
     *       {"tagid":1,"tagname":"a"},
     *       {"tagid":2,"tagname":"b"}
     *    ]
     * }
     */
    public function getTagList()
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX . '/tag/list?access_token=' . $this->access_token
        );
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
