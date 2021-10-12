<?php

namespace shiyunSdk\wechatSdk\common;

trait TraitWxCache
{
    /**
     * 设置缓存，按需重载
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired)
    {
        // TODO: set cache implementation
        $where = array();
        $where['access_token'] = $value;
        $where['expire_time'] = time();
        $where['expire_in'] = $expired;
        $apiResultData = ctoHttpCurl(_URL_API_ . "wx/opt", array(
            'type' => 'updateSet',
            'wx_id' => _TOOL_WX_SETT_ID_,
            'data' => $where
        ));
        $set = json_decode($apiResultData, true);
        if ($set['status'] == 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename)
    {
        // TODO: get cache implementation
        $apiResultData = ctoHttpCurl(_URL_API_ . "wx/opt", array(
            'type' => 'getSet',
            'wx_id' => _TOOL_WX_SETT_ID_
        ));
        $set = json_decode($apiResultData, true)['data'];
        if ((time() - $set['expire_in']) > $set['expire_time']) {
            return false;
        } else {
            return $set['access_token'];
        }
    }

    /**
     * 清除缓存，按需重载
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename)
    {
        // TODO: remove cache implementation
        return false;
    }
}
