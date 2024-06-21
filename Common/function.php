<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2019-02-21T09:58:42+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2021-03-18T10:39:17+08:00
 */
if (!function_exists('result')) {
    /**
     * [result 返回状态数组]
     * @param  [type] $code [错误码]
     * @param  string $data [具体数据]
     * @return [type]       [description]
     */
    // function result($code,$data=false)
    // {
    //     if (empty($data) || $data===true) {
    //         $result = array('data'=>$code ?: '$data数据为空');
    //     } else {
    //         $result = array('data'=>false, 'code'=>$code, 'err'=>$data);
    //     }
    //     return $result;
    // }

    function result($code, $msg=true, $data=false)
    {
        // dump(is_array($code));die;
        if (is_array($code) || $msg === true) {
            $result = array('data'=>$code);
            // dump(11111111);
        } else {
            $result = array('code'=>$code, 'err'=>$msg, 'data'=>$data);
            // dump(22222222);
        }
        return $result;
    }
}

if (!function_exists('msg')) {
    /**
     * [result 返回状态数组]
     * @param  [type] $code [错误码]
     * @param  string $data [具体数据]
     * @return [type]       [description]
     */
    function msg($code, $msg='操作成功', $data='')
    {
        $result = array('code'=>$code, 'msg'=>$msg, 'data'=>$data, 'time'=>time());
        return $result;
    }
}

if (!function_exists('p')) {
    /**
     * [dump 浏览器友好的变量输出]
     * @param  [type]  $var   [变量]
     * @param  boolean $echo  [是否输出 默认为true 如果为false 则返回输出字符串]
     * @param  [type]  $label [标签 默认为空]
     * @param  [type]  $flags [htmlspecialchars flags]
     * @return [type]         [description]
     */
    function p($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
    {
        $label = (null === $label) ? '' : rtrim($label) . ':';
        if ($var instanceof Model || $var instanceof ModelCollection) {
            $var = $var->toArray();
        }

        ob_start();
        var_dump($var);

        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $label . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, $flags);
            }
            $output = '<pre>' . $label . $output . '</pre>';
        }
        if ($echo) {
            echo($output);
            return;
        }
        return $output;
    }
}

/**    文件及文件夹处理      **/

if (!function_exists('write_in_txt')) {
    /**
     * [read_txt 日志写入文档]
     * @param  [type] $data [数组数据]
     */
    function write_in_txt($data)
    {
        $data = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
        $newLog ='log_time:'.date('Y-m-d H:i:s').'       '.$data;
        file_put_contents("../runtime/log.txt", $newLog.PHP_EOL, FILE_APPEND);
        return true;
    }
}

if (!function_exists('open_file')) {
    /**
     * [file_open 打开文件处理]
     * @param  [type] $url [路径]
     * @return [type]      [description]
     */
    function open_file($url, $type=false)
    {
        if ($url) {
            if (!stristr($url, 'http'))
                $url = './'.trim(trim($url, '.'), '/');
            if (stristr($url, 'http') && $type)
                $url = parse_url($str)['path'];

            $file = file_get_contents($url);
            return $file;
        } else {
            return false;
        }
    }
}

if (!function_exists('download_file')) {
    /**
     * [download_file 下载远程文件]
     * @param  [type]  $url       [远程图片地址]
     * @param  string  $save_path [保存路径（默认原始路径1当前目录）]
     * @param  string  $filename  [保存文件名称（默认原始文件名1随机）]
     * @param  boolean $replace   [是否同名覆盖]
     * @param  boolean $type      [使用的下载方式]
     * @return [type]             [description]
     */
    function download_file($url,$save_path='',$filename='',$replace=true,$type=false){
        if (!$url)
            return array('msg'=>'图片缺失','file_name'=>'','save_path'=>'');
        if (!$save_path)
            $save_path='.'.dirname(parse_url($url,PHP_URL_PATH));
            if ($save_path==1)
                $save_path='./';
            $save_path = './'.trim(trim($save_path,'.'),'/');

        if (!$filename) {
            $filename = basename($url);
            $filename == 1 && $filename = time().rand(1000,9999).strrchr($url,'.');
        }

        if(0!==strrpos($save_path,'/'))
            $save_path.='/';
        //创建保存目录
        if(!file_exists($save_path)&&!mkdir($save_path,0777,true))
            return array('msg'=>'创建目录失败','file_name'=>'','save_path'=>'');
        if(file_exists($save_path.$filename))
            if (!$replace)
                return array('msg'=>'该文件已存在','file_name'=>'','save_path'=>'');
            @unlink($save_path.$filename);

        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_path.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_path.$filename);
    }
}

if (!function_exists('del_file')) {
    /**
     * [del_file 删除文件及文件夹]
     * @param  [type]  $path [所在路径]
     * @param  boolean $type [是否删除当前目录文件]
     * @return [type]        [description]
     */
    function del_file($path, $type=false){
        // 先处理路径 去掉./后再次添加
        $path = './'.trim(trim($path,'.'),'/').'/';
        if(is_dir($path)){ //如果是目录则继续
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $files = scandir($path);
            foreach($files as $filename){
                if($filename !="." && $filename !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$filename)){
                        del_file($path.$filename.'/'); //子目录中操作删除文件夹和文件
                        @rmdir($path.$filename.'/'); //目录清空后删除空文件夹
                    }else{
                        unlink($path.$filename); //如果是文件直接删除
                    }
                }
            }
        }
        if ($type) { // 判断是否删除当前空文件夹
            @rmdir($path);
        }
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

if (!function_exists('image_url')) {
    /**
     * [image_url 图片路径处理]
     * @param  [type]  $str  [description]
     * @param  boolean $type [description]
     * @return [type]        [description]
     */
    function image_url($url, $type=false)
    {
        if (empty($url)) {
            return $url;
        }
        $images = is_array($url) ? $url : explode(',',$url);
        foreach ($images as $k => $v) {
            $v = trim($v, '.');
            $images[$k] = strstr($v, 'http') ? $v : SITE_URL.$v;
        }
        return !$type ? $images : $images[0];
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

if (!function_exists('Sec2Time')) {
    function Sec2Time($time) {
        if(is_numeric($time)){
            $str = '';
            if($time >= 31556926){
                $str .= floor($time/31556926) .'年';
                $time = ($time%31556926);
            }
            if($time >= 86400){
                $str .= floor($time/86400) .'天';
                $time = ($time%86400);
            }
            if($time >= 3600){
                $str .= floor($time/3600) .'时';
                $time = ($time%3600);
            }
            Return $str;

        }else{
            return (bool) FALSE;
        }
    }
}

if (!function_exists('msubstr')) {
    /** fengkui.net
     * [msubstr 截取的字符串]
     * @param  [type]  $str     [要截取的字符串]
     * @param  [type]  $length  [截取长度]
     * @param  integer $start   [开始位置，默认从0开始]
     * @param  string  $charset [字符编码，默认UTF－8]
     * @param  boolean $suffix  [是否在截取后的字符后面显示省略号，默认true显示，false为不显示]
     * @return [type]           [description]
     */
    //模版使用：{$vo.title|msubstr=0,5,'utf-8',false}
    function msubstr($str,  $length, $start = 0, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr")) {
            if ($suffix) return mb_substr($str, $start, $length, $charset)."...";
            else return mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            if ($suffix) return iconv_substr($str, $start, $length, $charset)."...";
            else return iconv_substr($str, $start, $length, $charset);
        }
        $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix)
            return $slice."…";
        return $slice;
    }
}

if (!function_exists('unicode_encode')) {
    /**
     * [unicode_encode Unicode编码]
     * @param  [type] $str      [原始字符串]
     * @param  string $encoding [原始字符串的编码，默认GBK]
     * @param  string $prefix   [编码后的前缀，默认"&#"]
     * @param  string $postfix  [编码后的后缀，默认";"]
     * @return [type]           [description]
     */
    function unicode_encode($str, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
        $str = iconv($encoding, 'UCS-2', $str);
        $arrstr = str_split($str, 2);
        $unistr = '';
        for($i = 0, $len = count($arrstr); $i < $len; $i++) {
            $dec = hexdec(bin2hex($arrstr[$i]));
            $unistr .= $prefix . $dec . $postfix;
        }
        return $unistr;
    }
}

if (!function_exists('unicode_decode')) {
    /**
     * [unicode_decode Unicode解码]
     * @param  [type] $unistr   [Unicode编码后的字符串]
     * @param  string $encoding [原始字符串的编码，默认GBK]
     * @param  string $prefix   [编码字符串的前缀，默认"&#"]
     * @param  string $postfix  [编码字符串的后缀，默认";"]
     * @return [type]           [description]
     */
    function unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
        $arruni = explode($prefix, $unistr);
        $unistr = '';
        for($i = 1, $len = count($arruni); $i < $len; $i++) {
            if (strlen($postfix) > 0) {
                $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
            }
            $temp = intval($arruni[$i]);
            $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
        }
        return iconv('UCS-2', $encoding, $unistr);
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

if (!function_exists('combine_dika')) {
    /**
     * [combine_dika combine_dika]
     * @return [type] [description]
     */
    function combine_dika() {
    	$data = func_get_args();
    	$data = current($data);
    	$cnt = count($data);
    	$result = array();
        $arr1 = array_shift($data);
    	foreach($arr1 as $key=>$item) {
    		$result[] = array($item);
    	}
    	foreach($data as $key=>$item) {
    		$result = combine_array($result,$item);
    	}
    	return $result;
    }
}

if (!function_exists('combine_array')) {
    /**
     * [combineArray 两个数组的笛卡尔积]
     * @param  [type] $arr1 [description]
     * @param  [type] $arr2 [description]
     * @return [type]       [description]
     */
    function combine_array($arr1,$arr2) {
    	$result = array();
    	foreach ($arr1 as $item1)
    	{
    		foreach ($arr2 as $item2)
    		{
    			$temp = $item1;
    			$temp[] = $item2;
    			$result[] = $temp;
    		}
    	}
    	return $result;
    }
}

if (!function_exists('num_letter')) {
    // 公共文件，十进制转二十六进制(基数为A-Z)
    function num_letter($num) {
        $num = intval($num);
        if ($num <= 0)
            return false;
        $letterArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $letter = '';
        do {
            $key = ($num - 1) % 26;
            $letter = $letterArr[$key] . $letter;
            $num = floor(($num - $key) / 26);
        } while ($num > 0);
        return $letter;
    }
}

if (!function_exists('get_html_data')) {
    /**
    * [get_html_data 使用xpath对获取到的html内容进行处理]
    * @param  [type] $html  [HTML内容]
    * @param  [type] $xpath [Xpath语句]
    * @param  [type] $tag   [类型 0内容 1标签内容 自定义标签]
    * @param  [type] $type  [单个 还是多个]
    * @return [type]        [description]
    */
    function get_html_data($html,$path,$tag=1)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html); // 从一个字符串加载HTML
        $dom->normalize(); // 使该HTML规范化
        $xpath = new DOMXPath($dom); //用DOMXpath加载DOM，用于查询
        $contents = $xpath->query($path); // 获取所有内容
        $data = [];
        foreach ($contents as $value) {
            if ($tag==1) {
                $data[] = $value->nodeValue; // 获取不带标签内容
            } elseif ($tag==2) {
                $data[] = $dom->saveHtml($value);  // 获取带标签内容
            } else {
                $data[] = $value->attributes->getNamedItem($tag)->nodeValue; // 获取attr内容
            }
        }
        if (count($data)==1) {
            $data = $data[0];
        }
        return $data;
    }
}

if (!function_exists('get_tag_data')) {
   /**
    * [get_tag_data 使用正则获取html内容]
    * @param  [type] $html  [需要爬取的页面内容]
    * @param  [type] $tag   [要查找的标签]
    * @param  [type] $attr  [要查找的属性名]
    * @param  [type] $value [属性名对应的值]
    * @return [type]        [description]
    */
    function get_tag_data($html,$tag,$attr,$value){
        $regex = "/<$tag.*?$attr=\".*?$value.*?\".*?>(.*?)<\/$tag>/is";
        preg_match_all($regex,$html,$matches,PREG_PATTERN_ORDER);
        $data = isset($matches[1][0]) ? $matches[1][0] : '';
        return $data;
    }
}

if (!function_exists('chn_discount')) {
    /**
     * [chn_discount 数字转中文折扣]
     * @param  [type] $num [description]
     * @return [type]      [description]
     */
    function chn_discount($num)
    {
        $num <= 1 && $num = $num*100;
        if ($num >= 100)
            return '';
        $chnnum = '';
        $datacn = array('零','一','二','三','四','五','六','七','八','九');
        $num = abs($num);
        $numstr = strval($num);
        $alen = strlen($numstr);
        $pos = $alen - 1;
        for ($i = 0; $i < $alen; $i++) {
            $curidx = intval($numstr[$i]);
            if ($curidx > 0) {
                $chnnum .= $datacn[$curidx];
            } else if (!$lastunit) {
                $chnnum .= $datacn[$curidx];
            }
            $pos--;
        }
        $chnnum || $chnnum .= '零';
        return $chnnum . '折';
    }
}
