<?php

namespace shiyunSdk\wechatGzh;

class GzhMsgMedia extends GzhBase
{

    /**
     * 上传临时素材，有效期为3天(认证后的订阅号可用)
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * 注意：临时素材的media_id是可复用的！
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
     * @return boolean|array
     */
    public function uploadMedia($data, $type)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        // 原先的上传多媒体文件接口使用 self::URL_UPLOAD_MEDIA 前缀
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX .  '/media/upload?access_token=' . $this->access_token . '&type=' . $type,
            $data,
            true
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
     * 获取临时素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return raw data
     */
    public function getMedia($media_id, $is_video = false)
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        // 原先的上传多媒体文件接口使用 self::URL_UPLOAD_MEDIA 前缀
        // 如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        $url_prefix = $is_video ? str_replace('https', 'http', self::URL_API_PREFIX) : self::URL_API_PREFIX;
        $result = $this->curlHttpGet(
            $url_prefix . '/media/get?access_token=' . $this->access_token . '&media_id=' . $media_id
        );
        if ($result) {
            if (is_string($result)) {
                $json = json_decode($result, true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
            }
            return $result;
        }
        return false;
    }

    /**
     * 上传永久素材(认证后的订阅号可用)
     * 新增的永久素材也可以在公众平台官网素材管理模块中看到
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
     * @param boolean $is_video 是否为视频文件，默认为否
     * @param array $video_info 视频信息数组，非视频素材不需要提供 array('title'=>'视频标题','introduction'=>'描述')
     * @return boolean|array
     */
    public function uploadForeverMedia($data, $type, $is_video = false, $video_info = array())
    {
        if (!$this->access_token && !$this->checkAuth())
            return false;
        // #TODO 暂不确定此接口是否需要让视频文件走http协议
        // 如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        // $url_prefix = $is_video?str_replace('https','http',self::URL_API_PREFIX):self::URL_API_PREFIX;
        // 当上传视频文件时，附加视频文件信息
        if ($is_video)
            $data['description'] = self::json_encode($video_info);
        $result = $this->curlHttpPost(
            self::URL_API_PREFIX . '/material/add_material?access_token=' . $this->access_token . '&type=' . $type,
            $data,
            true
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
