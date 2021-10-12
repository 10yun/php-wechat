<?php

namespace shiyunSdk\wechatQyh;

use shiyunSdk\wechatQyh\QyhBase;
use shiyunSdk\wechatSdk\libs\HelperCurl;

class GzhMsgMedia extends QyhBase
{
    /**
     * 上传多媒体文件 (只有三天的有效期，过期自动被删除)
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 媒体文件类型:图片（image）、语音（voice）、视频（video），普通文件(file)
     * @return boolean|array
     * {
     *    "type": "image",
     *    "media_id": "0000001",
     *    "created_at": "1380000000"
     * }
     */
    public function uploadMedia($data, $type)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpPost(
            self::URL_API_PREFIX . self::MEDIA_UPLOAD . 'access_token=' . $this->access_token . '&type=' . $type,
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
     * 根据媒体文件ID获取媒体文件
     * @param string $media_id 媒体文件id
     * @return raw data
     */
    public function getMedia($media_id)
    {
        if (!$this->access_token && !$this->wxAccessToken())
            return false;
        $result = HelperCurl::curlHttpGet(
            'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->access_token . '&media_id=' . $media_id
        );
        if ($result) {
            $json = json_decode($result, true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $result;
        }
        return false;
    }
}
