<?php

/**
 * App专属微信响应类
 */

namespace Util\Wx;

class Wxpayndsdk
{
    public $data; // 接收到的数据，类型为关联数组
    var $returnParameters; // 返回参数，类型为关联数组

    // 微信参数设置
    public $set;
    public function __construct($options)
    {
        $this->set = M('set')->find();
    }
    function trimString($value)
    {
        $ret = null;
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }
    /**
     * 将微信的请求xml转换成关联数组，以方便数据处理
     */
    function saveData($xml)
    {
        $this->data = $this->xmlToArray($xml);
    }
    /**
     * 	作用：生成签名
     */
    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        // 签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        // echo '【string1】'.$String.'</br>';
        // 签名步骤二：在string后加入KEY
        // $String = $String . "&key=13903072727139030727271390307272";
        // $String = $String . "&key=" . $this->mchkey;
        $String = $String . "&key=" . $this->set['wxmchkey'];

        // echo "【string2】".$String."</br>";
        // 签名步骤三：MD5加密
        $String = md5($String);
        // echo "【string3】 ".$String."</br>";
        // 签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        // echo "【result】 ".$result_."</br>";
        return $result_;
    }
    function checkSign()
    {
        $tmpData = $this->data;
        unset($tmpData['sign']);
        $sign = $this->getSign($tmpData); // 本地签名
        if ($this->data['sign'] == $sign) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 获取微信的请求数据
     */
    function getData()
    {
        return $this->data;
    }

    /**
     * 设置返回微信的xml数据
     */
    function setReturnParameter($parameter, $parameterValue)
    {
        $this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    /**
     * 生成接口参数xml
     */
    function createXml()
    {
        return $this->arrayToXml($this->returnParameters);
    }

    /**
     * 将xml数据返回微信
     */
    function returnXml()
    {
        $returnXml = $this->createXml();
        return $returnXml;
    }
}
