<?php

namespace shiyunSdk\wechatGzh;

use shiyunSdk\wechatSdk\libs\HelperCurl;

class GzhMsgArticle extends GzhBase
{


    /**
     * 上传永久图文素材(认证后的订阅号可用)
     * 新增的永久素材也可以在公众平台官网素材管理模块中看到
     * @param array $data 消息结构{"articles":[{...}]}
     * @return boolean|array
     */
    public function uploadForeverArticles($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/material/add_news?access_token=' . $this->access_token,
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
     * 修改永久图文素材(认证后的订阅号可用)
     * 永久素材也可以在公众平台官网素材管理模块中看到
     * @param string $media_id 图文素材id
     * @param array $data 消息结构{"articles":[{...}]}
     * @param int $index 更新的文章在图文素材的位置，第一篇为0，仅多图文使用
     * @return boolean|array
     */
    public function updateForeverArticles($media_id, $data, $index = 0)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        if (!isset($data['media_id']))
            $data['media_id'] = $media_id;
        if (!isset($data['index']))
            $data['index'] = $index;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/material/update_news?access_token=' . $this->access_token,
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
     * 获取永久素材(认证后的订阅号可用)
     * 返回图文消息数组或二进制数据，失败返回false
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return boolean|array|raw data
     */
    public function getForeverMedia($media_id, $is_video = false)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $data = array(
            'media_id' => $media_id
        );
        // #TODO 暂不确定此接口是否需要让视频文件走http协议
        // 如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        // $url_prefix = $is_video?str_replace('https','http',self::URL_API_PREFIX):self::URL_API_PREFIX;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/material/get_material?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            if (is_string($result)) {
                $json = json_decode($result, true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
                return $json;
            }
            return $result;
        }
        return false;
    }

    /**
     * 删除永久素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @return boolean
     */
    public function delForeverMedia($media_id)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $data = array(
            'media_id' => $media_id
        );
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/material/del_material?access_token=' . $this->access_token,
            self::json_encode($data)
        );
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取永久素材列表(认证后的订阅号可用)
     * @param string $type 素材的类型,图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 全部素材的偏移位置，0表示从第一个素材
     * @param int $count 返回素材的数量，取值在1到20之间
     * @return boolean|array
     * 返回数组格式:
     * array(
     *  'total_count'=>0, //该类型的素材的总数
     *  'item_count'=>0,  //本次调用获取的素材的数量
     *  'item'=>array()   //素材列表数组，内容定义请参考官方文档
     * )
     */
    public function getForeverList($type, $offset, $count)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $data = array(
            'type' => $type,
            'offset' => $offset,
            'count' => $count
        );
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/material/batchget_material?access_token=' . $this->access_token,
            self::json_encode($data)
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
     * 获取永久素材总数(认证后的订阅号可用)
     * @return boolean|array
     * 返回数组格式:
     * array(
     *  'voice_count'=>0, //语音总数量
     *  'video_count'=>0, //视频总数量
     *  'image_count'=>0, //图片总数量
     *  'news_count'=>0   //图文总数量
     * )
     */
    public function getForeverCount()
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            self::URL_API_PREFIX . '/material/get_materialcount?access_token=' . $this->access_token
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
     * 上传图文消息素材，用于群发(认证后的订阅号可用)
     * @param array $data 消息结构{"articles":[{...}]}
     * @return boolean|array
     */
    public function uploadArticles($data)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . '/media/uploadnews?access_token=' . $this->access_token,
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
