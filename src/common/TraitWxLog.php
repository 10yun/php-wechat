<?php

namespace shiyunWechat\common;

trait TraitWxLog
{
    // 日志LOG
    protected function zcLog($errcode, $errmsg)
    {
        $returnAy = array();
        $returnAy['errcode'] = $errcode;
        $returnAy['errmsg'] = $errmsg;
        $returnAy['errtime'] = date("Y-m-d H:i:s", time());
        $logfile = fopen("logfile_" . date("Ymd", time()) . ".txt", "a+");
        $txt = json_encode($returnAy) . "\n";
        fwrite($logfile, $txt);
        fclose($logfile);
        // return $returnAy;
    }
    // 日志记录2017-08-26
    protected function logger($log_content)
    {
        if (isset($_SERVER['HTTP_APPNAME'])) { // SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        } else if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1") { // LOCAL
            $max_size = 10000;
            $log_filename = _CTOCODE_RUNTIME_ . "/weixin/log.xml";
            if (file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)) {
                unlink($log_filename);
            }
            file_put_contents($log_filename, date('H:i:s') . " " . $log_content . "\r\n", FILE_APPEND);
        }
    }
    /**
     * 日志记录，可被重载。
     * @param mixed $log 输入日志
     * @return mixed
     */
    protected function log($log)
    {
        if ($this->debug) {
            if (function_exists($this->_logcallback)) {
                if (is_array($log)) {
                    $log = print_r($log, true);
                }
                return call_user_func($this->_logcallback, $log);
            } elseif (class_exists('Log')) {
                Log::write('wechat：' . $log, Log::DEBUG);
                Log::write('qywechat：' . $log, Log::DEBUG);
            }
        }
        return false;
    }
}
