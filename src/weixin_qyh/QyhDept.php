<?php

namespace shiyunSdk\wechatQyh;

use shiyunSdk\wechatQyh\QyhBase;
use shiyunSdk\wechatSdk\libs\HelperCurl;

class GzhQyDept extends QyhBase
{
    /**
     * 创建部门
     * @param array $data 	结构体为:
     * array (
     *     "name" => "邮箱产品组",   //部门名称
     *     "parentid" => "1"         //父部门id
     *     "order" =>  "1",            //(非必须)在父部门中的次序。从1开始，数字越大排序越靠后
     * )
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "created",  //对返回码的文本描述内容
     *   "id": 2               //创建的部门id。 
     * }
     */
    public function createDepartment($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/department/create?access_token=' . $this->access_token,
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
     * 更新部门
     * @param array $data 	结构体为:
     * array(
     *     "id" => "1"               //(必须)部门id
     *     "name" =>  "邮箱产品组",   //(非必须)部门名称
     *     "parentid" =>  "1",         //(非必须)父亲部门id。根部门id为1
     *     "order" =>  "1",            //(非必须)在父部门中的次序。从1开始，数字越大排序越靠后
     * )
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "updated"  //对返回码的文本描述内容
     * }
     */
    public function updateDepartment($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/department/update?access_token=' . $this->access_token,
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
     * 删除部门
     * @param $id
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "deleted"  //对返回码的文本描述内容
     * }
     */
    public function deleteDepartment($id)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/department/delete?access_token=' . $this->access_token . '&id=' . $id
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
     * 移动部门
     * @param $data
     * array(
     *    "department_id" => "5",	//所要移动的部门
     *    "to_parentid" => "2",		//想移动到的父部门节点，根部门为1
     *    "to_position" => "1"		//(非必须)想移动到的父部门下的位置，1表示最上方，往后位置为2，3，4，以此类推，默认为1
     * )
     * @return boolean|array 成功返回结果
     * {
     *   "errcode": 0,        //返回码
     *   "errmsg": "ok"  //对返回码的文本描述内容
     * }
     */
    public function moveDepartment($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX .  '/department/move?access_token=' . $this->access_token,
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
     * 获取部门列表
     * @return boolean|array	 成功返回结果
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "department": [          //部门列表数据。以部门的order字段从小到大排列
     *        {
     *            "id": 1,
     *            "name": "广州研发中心",
     *            "parentid": 0
     *        },
     *       {
     *          "id": 2
     *          "name": "邮箱产品部",
     *          "parentid": 1
     *       }
     *    ]
     * }
     */
    public function getDepartment()
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX . '/department/list?access_token=' . $this->access_token
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
