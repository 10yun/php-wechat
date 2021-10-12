<?php

namespace shiyunSdk\wechatGzh;

class GzhUserGroup extends GzhBase
{

    /**
     * 获取用户分组列表
     * @return boolean|array
     */
    public function getGroup()
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $result = $this->curlHttpGet(
            self::URL_API_PREFIX .  '/groups/get?access_token=' . $this->access_token
        );
        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取用户所在分组
     * @param string $openid
     * @return boolean|int 成功则返回用户分组id
     */
    public function getUserGroup($openid)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'openid' => $openid
        );
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX . '/groups/getid?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            } else if (isset($json['groupid']))
                return $json['groupid'];
        }
        return false;
    }

    /**
     * 新增自定分组
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function createGroup($name)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'group' => array(
                'name' => $name
            )
        );
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX . '/groups/create?access_token=' . $this->access_token,
            self::json_encode($data)
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

    /**
     * 更改分组名称
     * @param int $groupid 分组id
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function updateGroup($groupid, $name)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'group' => array(
                'id' => $groupid,
                'name' => $name
            )
        );
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX . '/groups/update?access_token=' . $this->access_token,
            self::json_encode($data)
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

    /**
     * 移动用户分组
     * @param int $groupid 分组id
     * @param string $openid 用户openid
     * @return boolean|array
     */
    public function updateGroupMembers($groupid, $openid)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'openid' => $openid,
            'to_groupid' => $groupid
        );
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX . '/groups/members/update?access_token=' . $this->access_token,
            self::json_encode($data)
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

    /**
     * 批量移动用户分组
     * @param int $groupid 分组id
     * @param string $openid_list 用户openid数组,一次不能超过50个
     * @return boolean|array
     */
    public function batchUpdateGroupMembers($groupid, $openid_list)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        $data = array(
            'openid_list' => $openid_list,
            'to_groupid' => $groupid
        );
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX .  '/groups/members/batchupdate?access_token=' . $this->access_token,
            self::json_encode($data)
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
