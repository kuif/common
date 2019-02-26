<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2019-02-21T09:58:42+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2019-02-26T14:21:19+08:00
 */
if (!function_exists('result')) {
    /**
     * [result 返回状态数组]
     * @param  [type] $code [错误码]
     * @param  string $data [具体数据]
     * @return [type]       [description]
     */
    function result($code,$data='')
    {
        if (is_numeric($code) && $data!==true) {
            $result = array('data'=>false, 'code'=>$code, 'err'=>$data);
        } else {
            $result = array('data'=>$code);
        }

        return $result;
        die;
    }
}

if (!function_exists('write_in_txt')) {
    /**
     * [read_txt 日志写入文档]
     * @param  [type] $data [数组数据]
     */
    function write_in_txt($data)
    {
        $newLog ='log_time:'.date('Y-m-d H:i:s').'       '.json_encode($data);
        file_put_contents("./log.txt", $newLog.PHP_EOL, FILE_APPEND);
    }
}

/**    字符串处理      **/

if (!function_exists('strs_to_array')) {
    /** fengkui.net
     * [strs_to_array 字符串转数组]
     * @param  [type] $strs [传入数组]
     * @return [type]       [description]
     * transform ' hello, world !' to array('hello', 'world')
     */
    function strs_to_array($strs) {
        $result = array();
        $array = array();
        $strs = str_replace('，', ',', $strs);
        $strs = str_replace("n", ',', $strs);
        $strs = str_replace("rn", ',', $strs);
        $strs = str_replace(' ', ',', $strs);
        $array = explode(',', $strs);
        foreach ($array as $key => $value) {
            if ('' != ($value = trim($value))) {
                $result[] = $value;
            }
        }
        return $result;
    }
}

if (!function_exists('get_rand_str')) {
    /**
     * [get_rand_str 获取随机字符串]
     * @param  integer $randLength [字符串长度]
     * @param  integer $type       [字符串类型]
     * @param  integer $addtime    [是否加入当前时间戳]
     * @return [type]              [description]
     */
    function get_rand_str($randLength=6,$type=0,$addtime=0){
        if ($type==0){
            $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
        } elseif ($type==1) {
            $chars='0123456789';
        } else {
            $chars='abcdefghijklmnopqrstuvwxyz';
        }
        $len=strlen($chars);
        $randStr='';
        for ($i=0;$i<$randLength;$i++){
            $randStr.=$chars[rand(0,$len-1)];
        }
        $tokenvalue=$randStr;
        if ($addtime){
            $tokenvalue=$randStr.time();
        }
        return $tokenvalue;
    }
}

if (!function_exists('msubstr')) {
    /** fengkui.net
     * [msubstr 截取的字符串]
     * @param  [type]  $str     [要截取的字符串]
     * @param  integer $start   [开始位置，默认从0开始]
     * @param  [type]  $length  [截取长度]
     * @param  string  $charset [字符编码，默认UTF－8]
     * @param  boolean $suffix  [是否在截取后的字符后面显示省略号，默认true显示，false为不显示]
     * @return [type]           [description]
     */
    //模版使用：{$vo.title|msubstr=0,5,'utf-8',false}
    function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr")) {
            if ($suffix) return mb_substr($str, $start, $length, $charset)."...";
            else return mb_substr($str, $start, $length, $charset);
        }
        elseif(function_exists('iconv_substr')) {
            if ($suffix) return iconv_substr($str, $start, $length, $charset)."...";
            else return iconv_substr($str, $start, $length, $charset);
        }
        $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef]
                      [x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix) return $slice."…";
        return $slice;
    }
}

if (!function_exists('bank_info')) {
    /**
     * [bank_info 获取银行卡信息]
     * @param  [type] $card [description]
     * @return [type]       [description]
     */
    function bank_info($card) {
        $bankList = new feng\bankList();
        $bankList = $bankList->bankList;

        $result = false;
        foreach(array(4,5,6,8) as $n) {
            $tmp = substr($card, 0, $n);
            if (isset($bankList[$tmp])) {
                $result = $bankList[$tmp];
                break;
            }
        }
        return $result;
    }
}
