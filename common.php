<?php

header("Content-type:text/html;charset=utf-8");
/** fengkui.net
                            其他相关数据
 */

/** fengkui.net
 * [p 传递数据以易于阅读的样式格式化后输出]
 * @param  [type] $data [数组]
 * @return [type]       [description]
 */
function p($data)
{
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}

/** fengkui.net
 * [getOS 判断当前服务器系统]
 * @return [type] [description]
 */
function getOS()
{
    if(PATH_SEPARATOR == ':'){
        return 'Linux';
    }else{
        return 'Windows';
    }
}

/** fengkui.net
 * [getIP 定义一个函数getIP() 客户端IP]
 * @return [type] [description]
 */
function getIP()
{            
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";
    
    if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))          
        return $ip;
    else
        return '';
}

/** fengkui.net
 * [serverIP 服务器端IP]
 * @return [type] [description]
 */
function serverIP()
{   
    return gethostbyname($_SERVER["SERVER_NAME"]);   
}  

/** fengkui.net
 * [check_mobile 检查手机号码格式]
 * @param  [type] $mobile [手机号码]
 * @return [type]         [description]
 */
function check_mobile($mobile)
{
    if(preg_match('/1[34578]\d{9}$/',$mobile))
        return true;
    return false;
}

/** fengkui.net
 * [check_email 检查邮箱地址格式]
 * @param  [type] $email [邮箱地址]
 * @return [type]        [description]
 */
function check_email($email)
{
    if(filter_var($email,FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}

/** fengkui.net
 * [get_device_type 获取当前访问的设备类型]
 * @return [type] [1：其他  2：iOS  3：Android]
 */
function get_device_type()
{
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 1;
    //分别进行判断
    if(strpos($agent, 'iphone')!==false || strpos($agent, 'ipad')!==false){
        $type = 2;
    } 
    if(strpos($agent, 'android')!==false){
        $type = 3;
    }
    return $type;
}

/** fengkui.net
 * [isMobile 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端]
 * @return boolean [description]
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    return true;
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
    // 找不到为flase,否则为true
    return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
        // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
    // 如果只支持wml并且不支持html那一定是移动设备
    // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
            return false;
} 

/** fengkui.net
 * [file_category 根据文件后缀的不同返回不同的结果]
 * @param  [type] $str [需要判断的文件名或者文件的id]
 * @return [type]      [1:图片  2：视频  3：压缩文件  4：文档  5：其他]
 */
function file_category($str)
{
    // 取文件后缀名
    $str=strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $images=array('webp','jpg','png','ico','bmp','gif','tif','pcx','tga','bmp','pxc','tiff','jpeg','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','hdri');
    // 视频格式
    $video=array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack');
    // 压缩格式
    $zip=array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','tz');
    // 文档格式
    $document=array('exe','doc','ppt','xls','wps','txt','lrc','wfs','torrent','html','htm','java','js','css','less','php','pdf','pps','host','box','docx','word','perfect','dot','dsf','efe','ini','json','lnk','log','msi','ost','pcs','tmp','xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $images):
            return 1;
            break;
        case in_array($str, $video):
            return 2;
            break;
        case in_array($str, $zip):
            return 3;
            break;
        case in_array($str, $document):
            return 4;
            break;
        default:
            return 5;
            break;
    }
}

/** fengkui.net
 * [byteFormat 格式化单位]
 * @param  [type]  $size [大小]
 * @param  integer $dec  [description]
 * @return [type]        [description]
 */
function byteFormat( $size, $dec = 2 ) 
{
    $a = array ( "B" , "KB" , "MB" , "GB" , "TB" , "PB" );
    $pos = 0;
    while ( $size >= 1024 ) {
        $size /= 1024;
        $pos ++;
    }
    return round( $size, $dec ) . " " . $a[$pos];
}

/** fengkui.net
 * [selected 下拉框，单选按钮 自动选择]
 * @param  [type]  $string [输入字符]
 * @param  integer $param  [条件]
 * @param  string  $type   [类型]
 * @return [type]          [description]
 */
function selected( $string, $param = 1, $type = 'select' ) 
{
    $true = false;
    if ( is_array( $param ) ) {
        $true = in_array( $string, $param );
    }elseif ( $string == $param ) {
        $true = true;
    }
    $return='';
    if ( $true )
        $return = $type == 'select' ? 'selected="selected"' : 'checked="checked"';
    echo $return;
}

/** fengkui.net
                                            数组操作
 */
/** fengkui.net
 * [convert_arr_key 将数据库中查出的列表以指定的 id 作为数组的键名 ]
 * @param  [type] $arr      [数组]
 * @param  [type] $key_name [ID]
 * @return [type]           [description]
 */
function convert_arr_key($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val;        
    }
    return $arr2;
}

/** fengkui.net
 * [get_id_val 将数据库中查出的列表以指定的 id 作为数组的键名 数组指定列为元素 的一个数组]
 * @param  [type] $arr       [数组]
 * @param  [type] $key_name  [key]
 * @param  [type] $key_name2 [value]
 * @return [type]            [description]
 */
function get_id_val($arr, $key_name,$key_name2)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val[$key_name2];
    }
    return $arr2;
}

/** fengkui.net
 * [get_arr_column 返回那一列的数组]
 * @param  [type] $arr      [数组]
 * @param  [type] $key_name [列名]
 * @return [type]           [description]
 */
function get_arr_column($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[] = $val[$key_name];        
    }
    return $arr2;
}

/** fengkui.net
 * [array_sort 二维数组排序]
 * @param  [type] $arr  [数组]
 * @param  [type] $keys [键值]
 * @param  string $type [排序方式]
 * @return [type]       [description]
 */
function array_sort($arr, $keys, $type = 'desc')
{
    $key_value = $new_array = array();
    foreach ($arr as $k => $v) {
        $key_value[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($key_value);
    } else {
        arsort($key_value);
    }
    reset($key_value);
    foreach ($key_value as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/** fengkui.net
 * [array_multi2_single 多维数组转化为一维数组]
 * @param  [type] $array [数组]
 * @return [type]        [description]
 */
function array_multi2_single($array)
{
    static $result_array = array();
    foreach ($array as $value) {
        if (is_array($value)) {
            array_multi2single($value);
        } else
            $result_array [] = $value;
    }
    return $result_array;
}

/** fengkui.net
 * [combineDika 多个数组的笛卡尔积]
 * @return [type] [description]
 */
function combineDika() 
{
    $data = func_get_args();
    $data = current($data);
    $cnt = count($data);
    $result = array();
    $arr1 = array_shift($data);
    foreach($arr1 as $key=>$item) 
    {
        $result[] = array($item);
    }       

    foreach($data as $key=>$item) 
    {                                
        $result = combineArray($result,$item);
    }
    return $result;
}

/** fengkui.net
 * [combineArray 两个数组的笛卡尔积]
 * @param  [type] $arr1 [数组1]
 * @param  [type] $arr2 [数组2]
 * @return [type]       [description]
 */
function combineArray($arr1,$arr2) 
{         
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

/** fengkui.net
 * [in_array 字符是否在数组中 不区分大小写]
 * @param  [type] $str   [检测的字符]
 * @param  [type] $array [数组]
 * @return [type]        [description]
 */
function in_array($str,$array)
{
    $str=strtolower($str);
    $array=array_map('strtolower', $array);
    if (in_array($str, $array)) {
        return true;
    }
    return false;
}

/** fengkui.net
 * [group_same_key 将二维数组以元素的某个值作为键 并归类数组]
 * array( array('name'=>'aa','type'=>'pay'), array('name'=>'cc','type'=>'pay') )
 * array('pay'=>array( array('name'=>'aa','type'=>'pay') , array('name'=>'cc','type'=>'pay') ))
 * @param  [type] $arr [数组]
 * @param  [type] $key [分组值的key]
 * @return [type]      [description]
 */
function group_same_key($arr,$key)
{
    $new_arr = array();
    foreach($arr as $k=>$v ){
        $new_arr[$v[$key]][] = $v;
    }
    return $new_arr;
}

/** fengkui.net
 * [pdf 生成pdf]
 * @param  string $html [需要生成的内容]
 * @return [type]       [description]
 */
function pdf($html='<h1 style="color:red">hello word</h1>')
{
    vendor('Tcpdf.tcpdf');
    $pdf = new \Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // 设置打印模式
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nicola Asuni');
    $pdf->SetTitle('TCPDF Example 001');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    // 是否显示页眉
    $pdf->setPrintHeader(false);
    // 设置页眉显示的内容
    $pdf->SetHeaderData('logo.png', 60, 'baijunyao.com', '白俊遥博客', array(0,64,255), array(0,64,128));
    // 设置页眉字体
    $pdf->setHeaderFont(Array('dejavusans', '', '12'));
    // 页眉距离顶部的距离
    $pdf->SetHeaderMargin('5');
    // 是否显示页脚
    $pdf->setPrintFooter(true);
    // 设置页脚显示的内容
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    // 设置页脚的字体
    $pdf->setFooterFont(Array('dejavusans', '', '10'));
    // 设置页脚距离底部的距离
    $pdf->SetFooterMargin('10');
    // 设置默认等宽字体
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // 设置行高
    $pdf->setCellHeightRatio(1);
    // 设置左、上、右的间距
    $pdf->SetMargins('10', '10', '10');
    // 设置是否自动分页  距离底部多少距离时分页
    $pdf->SetAutoPageBreak(TRUE, '15');
    // 设置图像比例因子
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->setFontSubsetting(true);
    $pdf->AddPage();
    // 设置字体
    $pdf->SetFont('stsongstdlight', '', 14, '', true);
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('example_001.pdf', 'I');
}

/** fengkui.net
 * [create_xls 数组转xls格式的excel文件]
 * @param  [type] $data     [需要生成excel文件的数组]
 * @param  string $filename [生成的excel文件名]
 * @return [type]           [description]
 *      示例数据：
 *      $data = array(
 *         array(NULL, 2010, 2011, 2012),
 *         array('Q1',   12,   15,   21),
 *         array('Q2',   56,   73,   86),
 *         array('Q3',   52,   61,   69),
 *         array('Q4',   30,   32,    0),
 *          );
 */
function create_xls($data,$filename='simple.xls')
{
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}

/** fengkui.net
 * [create_csv 数据转csv格式的excel]
 * @param  [type] $data     [需要转的数组]
 * @param  string $filename [生成的excel文件名]
 * @return [type]           [description]
 *      示例数组：
 *       $a = array(
 *           '1,2,3,4,5',
 *           '6,7,8,9,0',
 *           '1,3,5,6,7'
 *            );
 */
function create_csv($data,$filename='simple.csv')
{
    // 防止没有添加文件后缀
    $filename=str_replace('.csv', '', $filename).'.csv';
    Header( "Content-type:  application/octet-stream ");
    Header( "Accept-Ranges:  bytes ");
    Header( "Content-Disposition:  attachment;  filename=".$filename);
    foreach( $data as $k => $v){
        // 替换掉换行
        $v=preg_replace('/\s*/', '', $v); 
        // 转成gbk以兼容office乱码的问题
        echo iconv('UTF-8','GBK',$v)."\r\n";
    }
}

/** fengkui.net
                                字符串操作
 */
/** fengkui.net
 * [rand_number 获取一定范围内的随机数字]
 * @param  integer $min [最小值]
 * @param  integer $max [最大值]
 * @return [type]       [description]
 */
function rand_number ($min=1, $max=9999) 
{
    return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
}

/** fengkui.net
 * [get_rand_number 生成不重复的随机数]
 * @param  integer $start  [需要生成的数字开始范围]
 * @param  integer $end    [结束范围]
 * @param  integer $length [需要生成的随机数个数]
 * @return [type]          [生成的随机数]
 */
function get_rand_number($start=1,$end=10,$length=4)
{
    $connt=0;
    $temp=array();
    while($connt<$length){
        $temp[]=mt_rand($start,$end);
        $data=array_unique($temp);
        $connt=count($data);
    }
    sort($data);
    return $data;
}

/** fengkui.net
 * [build_count_rand 生成一定数量的随机数，并且不重复]
 * @param  [type]  $number [数量]
 * @param  integer $length [长度]
 * @param  integer $mode   [字串类型 0 字母 1 数字 其它 混合]
 * @return [type]          [description]
 */
function build_count_rand ($number,$length=4,$mode=1) 
{
    if($mode==1 && $length<strlen($number) ) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++) {
        $rand[] = rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand)) {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] = rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}

/** fengkui.net
 * [get_rand_str 获取随机字符串]
 * @param  integer $randLength    [长度]
 * @param  integer $addtime       [是否加入当前时间戳]
 * @param  integer $includenumber [是否包含数字]
 * @return [type]                 [description]
 */
function get_rand_str($randLength=6,$addtime=1,$includenumber=0)
{
    if ($includenumber){
        $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
    }else {
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

/** fengkui.net
 * [get_substr 实现中文字串截取无乱码的方法]
 * @param  [type] $string [字符串]
 * @param  [type] $start  [开始位置]
 * @param  [type] $length [长度]
 * @return [type]         [description]
 */
function get_substr($string, $start, $length) 
{
    if(mb_strlen($string,'utf-8')>$length){
        $str = mb_substr($string, $start, $length,'utf-8');
        return $str.'...';
    }else{
        return $string;
    }
}

/** fengkui.net
 * [cut_str 按符号截取字符串的指定部分]
 * @param  [type] $str    [需要截取的字符串]
 * @param  [type] $sign   [需要截取的符号]
 * @param  [type] $number [如是正数以0为起点从左向右截  负数则从右向左截]
 * @return [type]         [返回截取的内容]
 */
/*  示例
    $str='123/456/789';
    cut_str($str,'/',0);  返回 123
    cut_str($str,'/',-1);  返回 789
    cut_str($str,'/',-2);  返回 456
    具体参考 http://www.baijunyao.com/index.php/Home/Index/article/aid/18
*/
function cut_str($str,$sign,$number)
{
    $array=explode($sign, $str);
    $length=count($array);
    if($number<0){
        $new_array=array_reverse($array);
        $abs_number=abs($number);
        if($abs_number>$length){
            return 'error';
        }else{
            return $new_array[$abs_number-1];
        }
    }else{
        if($number>=$length){
            return 'error';
        }else{
            return $array[$number];
        }
    }
}

/** fengkui.net
 * [trim_array_element 过滤数组元素前后空格 (支持多维数组)]
 * @param  [type] $array [要过滤的数组]
 * @return [type]        [description]
 */
function trim_array_element($array)
{
    if(!is_array($array))
        return trim($array);
    return array_map('trim_array_element',$array);
}

/** fengkui.net
 * [get_first_letter php获取中文字符拼音首字母]
 * @param  [type] $str [字符串]
 * @return [type]      [description]
 */
function get_first_letter($str)
{
    if(empty($str))
    {
        return '';          
    }
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}

/** fengkui.net
 * [formatStr 格式化字符串]
 * @param  [type] $str [字符串]
 * @return [type]      [description]
 */
function formatStr($str) 
{
    $arr = array(' ', ' ', '&', '@', '#', '%',  '\'', '"', '\\', '/', '.', ',', '$', '^', '*', '(', ')', '[', ']', '{', '}', '|', '~', '`', '?', '!', ';', ':', '-', '_', '+', '=');
    foreach ($arr as $v) {
        $str = str_replace($v, '', $str);
    }
    return $str;
} 

/** fengkui.net
 * [auth_code 加密处理]
 * @param  [type]  $string    [原文或者密文]
 * @param  string  $operation [操作(ENCODE | DECODE), 默认为 DECODE]
 * @param  string  $key       [密钥]
 * @param  integer $expiry    [密文有效期, 加密时候有效， 单位 秒，0 为永久有效]
 * @return [type]             [处理后的 原文或者 经过 base64_encode 处理后的密文]
 * $a = authcode('abc', 'ENCODE', 'key');
 * $b = authcode($a, 'DECODE', 'key');  // $b(abc)
 * $a = authcode('abc', 'ENCODE', 'key', 3600);
 * $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
 */
function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 3600) 
{
    $ckey_length = 4;
    // 随机密钥长度 取值 0-32;
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥
    $key = md5 ( $key ? $key : 'key' ); //这里可以填写默认key值
    $keya = md5 ( substr ( $key, 0, 16 ) );
    $keyb = md5 ( substr ( $key, 16, 16 ) );
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';
    $cryptkey = $keya . md5 ( $keya . $keyc );
    $key_length = strlen ( $cryptkey );
    $string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
    $string_length = strlen ( $string );
    $result = '';
    $box = range ( 0, 255 );
    $rndkey = array ();
    for($i = 0; $i <= 255; $i ++) {
        $rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
    }
    for($j = $i = 0; $i < 256; $i ++) {
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;
        $tmp = $box [$i];
        $box [$i] = $box [$j];
        $box [$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i ++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box [$a]) % 256;
        $tmp = $box [$a];
        $box [$a] = $box [$j];
        $box [$j] = $tmp;
        $result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
    }
    if ($operation == 'DECODE') {
        if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )) {
            return substr ( $result, 26 );
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
    }
}

/** fengkui.net
 * [safeEncoding utf-8和gb2312自动转化]
 * @param  [type] $string      [字符串]
 * @param  string $outEncoding [类型]
 * @return [type]              [description]
 */
function safeEncoding($string,$outEncoding = 'UTF-8')
{
    $encoding = "UTF-8";
    for($i = 0; $i < strlen ( $string ); $i ++) {
        if (ord ( $string {$i} ) < 128)
            continue;
        if ((ord ( $string {$i} ) & 224) == 224) {
            // 第一个字节判断通过
            $char = $string {++ $i};
            if ((ord ( $char ) & 128) == 128) {
                // 第二个字节判断通过
                $char = $string {++ $i};
                if ((ord ( $char ) & 128) == 128) {
                    $encoding = "UTF-8";
                    break;
                }
            }
        }
        if ((ord ( $string {$i} ) & 192) == 192) {
            // 第一个字节判断通过
            $char = $string {++ $i};
            if ((ord ( $char ) & 128) == 128) {
                // 第二个字节判断通过
                $encoding = "GB2312";
                break;
            }
        }
    }
    if (strtoupper ( $encoding ) == strtoupper ( $outEncoding ))
        return $string;
    else
        return @iconv ( $encoding, $outEncoding, $string );
}

/** fengkui.net
 * [utf8_gb2312 判断字符串是utf-8 还是gb2312]
 * @param  [type] $str     [字符串]
 * @param  string $default [类型]
 * @return [type]          [description]
 */
function utf8_gb2312($str, $default = 'gb2312')
{
    $str = preg_replace("/[\x01-\x7F]+/", "", $str);
    if (empty($str)) return $default;

    $preg =  array(
        "gb2312" => "/^([\xA1-\xF7][\xA0-\xFE])+$/", //正则判断是否是gb2312
        "utf-8" => "/^[\x{4E00}-\x{9FA5}]+$/u",      //正则判断是否是汉字(utf8编码的条件了)，这个范围实际上已经包含了繁体中文字了
    );

    if ($default == 'gb2312') {
        $option = 'utf-8';
    } else {
        $option = 'gb2312';
    }

    if (!preg_match($preg[$default], $str)) {
        return $option;
    }
    $str = @iconv($default, $option, $str);

    //不能转成 $option, 说明原来的不是 $default
    if (empty($str)) {
        return $option;
    }
    return $default;
}

/** fengkui.net
 * [escape_sequence_decode 将utf-16的emoji表情转为utf8文字形]
 * @param  [type] $str [需要转的字符串]
 * @return [type]      [转完成后的字符串]
 */
function escape_sequence_decode($str) 
{
    $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';
    return preg_replace_callback($regex, function($matches) {
        if (isset($matches[3])) {
            $cp = hexdec($matches[3]);
        } else {
            $lead = hexdec($matches[1]);
            $trail = hexdec($matches[2]);
            $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
        }

        if ($cp > 0xD7FF && 0xE000 > $cp) {
            $cp = 0xFFFD;
        }
        if ($cp < 0x80) {
            return chr($cp);
        } else if ($cp < 0xA0) {
            return chr(0xC0 | $cp >> 6).chr(0x80 | $cp & 0x3F);
        }
        $result =  html_entity_decode('&#'.$cp.';');
        return $result;
    }, $str);
}

/** fengkui.net
 * [emoji_encode 把用户输入的文本转义（主要针对特殊符号和emoji表情）]
 * @param  [type] $str [字符串]
 * @return [type]      [description]
 */
function emoji_encode($str)
{
    if(!is_string($str))return $str;
    if(!$str || $str=='undefined')return '';

    $text = json_encode($str); //暴露出unicode
    $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
        return addslashes($str[0]);
    },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
    return json_decode($text);
}

/** fengkui.net
 * [get_ueditor_image_path 传递ueditor生成的内容获取其中图片的路径]
 * @param  [type] $str [含有图片链接的字符串]
 * @return [type]      [匹配的图片数组]
 */
function get_ueditor_image_path($str)
{
    //$preg='/\/Upload\/zhuanti\/u(m)?editor\/\d*\/\d*\.[jpg|jpeg|png|bmp]*/i';
    $preg='<img(.*?)src=\"(.*?)\"(.*?)/>';
    preg_match_all($preg, $str,$data);
    return current($data);
}

/** fengkui.net
 * [strip_html_tags 删除指定的标签和内容]
 * @param  [type]  $tags    [需要删除的标签数组]
 * @param  [type]  $str     [数据源]
 * @param  integer $content [是否删除标签内的内容 0保留内容 1不保留内容]
 * @return [type]           [description]
 */
function strip_html_tags($tags,$str,$content=0)
{
    if($content){
        $html=array();
        foreach ($tags as $tag) {
            $html[]='/(<'.$tag.'.*?>[\s|\S]*?<\/'.$tag.'>)/';
        }
        $data=preg_replace($html,'',$str);
    }else{
        $html=array();
        foreach ($tags as $tag) {
            $html[]="/(<(?:\/".$tag."|".$tag.")[^>]*>)/i";
        }
        $data=preg_replace($html, '', $str);
    }
    return $data;
}

/** fengkui.net
                      文件操作
 */
/** fengkui.net
 * [recurse_copy 自定义函数递归的复制带有多级子目录的目录]
 * @param  [type] $src [原目录]
 * @param  [type] $dst [复制到的目录]
 * @return [type]      [description]
 */
function recurse_copy($src, $dst)
{
    $now = time();
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== $file = readdir($dir)) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            }
            else {
                if (file_exists($dst . DIRECTORY_SEPARATOR . $file)) {
                    if (!is_writeable($dst . DIRECTORY_SEPARATOR . $file)) {
                        exit($dst . DIRECTORY_SEPARATOR . $file . '不可写');
                    }
                    @unlink($dst . DIRECTORY_SEPARATOR . $file);
                }
                if (file_exists($dst . DIRECTORY_SEPARATOR . $file)) {
                    @unlink($dst . DIRECTORY_SEPARATOR . $file);
                }
                $copyrt = copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                if (!$copyrt) {
                    echo 'copy ' . $dst . DIRECTORY_SEPARATOR . $file . ' failed<br>';
                }
            }
        }
    }
    closedir($dir);
}

/** fengkui.net
 * [del_file 递归删除文件夹]
 * @param  [type] $dir       [路径]
 * @param  string $file_type [文件夹名称]
 * @return [type]            [description]
 */
function del_file($dir,$file_type='') 
{
    if(is_dir($dir)){
        $files = scandir($dir);
        //打开目录 //列出目录中的所有文件并去掉 . 和 ..
        foreach($files as $filename){
            if($filename!='.' && $filename!='..'){
                if(!is_dir($dir.'/'.$filename)){
                    if(empty($file_type)){
                        unlink($dir.'/'.$filename);
                    }else{
                        if(is_array($file_type)){
                            //正则匹配指定文件
                            if(preg_match($file_type[0],$filename)){
                                unlink($dir.'/'.$filename);
                            }
                        }else{
                            //指定包含某些字符串的文件
                            if(false!=stristr($filename,$file_type)){
                                unlink($dir.'/'.$filename);
                            }
                        }
                    }
                }else{
                    delFile($dir.'/'.$filename);
                    rmdir($dir.'/'.$filename);
                }
            }
        }
    }else{
        if(file_exists($dir)) unlink($dir);
    }
}

/** fengkui.net
 * [down_file 下载文件]
 * @param  [type] $file_path [绝对路径]
 * @return [type]            [description]
 */
function down_file($file_path) 
{
    //判断文件是否存在
    $file_path = iconv('utf-8', 'gb2312', $file_path); //对可能出现的中文名称进行转码
    if (!file_exists($file_path)) {
        exit('文件不存在！');
    }
    $file_name = basename($file_path); //获取文件名称
    $file_size = filesize($file_path); //获取文件大小
    $fp = fopen($file_path, 'r'); //以只读的方式打开文件
    header("Content-type: application/octet-stream");
    header("Accept-Ranges: bytes");
    header("Accept-Length: {$file_size}");
    header("Content-Disposition: attachment;filename={$file_name}");
    $buffer = 1024;
    $file_count = 0;
    //判断文件是否结束
    while (!feof($fp) && ($file_size-$file_count>0)) {
        $file_data = fread($fp, $buffer);
        $file_count += $buffer;
        echo $file_data;
    }
    fclose($fp); //关闭文件
}

/** fengkui.net
 * [get_dir 取得输入目录所包含的所有目录和文件]
 * @param  [type] $dir [目录]
 * @return [type]      [description]
 */
function get_dir($dir)
{
    $fileArr = array();
    $dirArr = array();
    $dir = rtrim($dir, '//');
    if(is_dir($dir)){
        $dirHandle = opendir($dir);
        while(false !== ($fileName = readdir($dirHandle))){
            $subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
            if(is_file($subFile)){
                $fileArr[] = $subFile;
            } elseif (is_dir($subFile) && str_replace('.', '', $fileName)!=''){
                $dirArr[] = $subFile;
                $arr = self::deepScanDir($subFile);
                $dirArr = array_merge($dirArr, $arr['dir']);
                $fileArr = array_merge($fileArr, $arr['file']);
            }
        }
        closedir($dirHandle);
    }
    return array('dir'=>$dirArr, 'file'=>$fileArr);
}

/** fengkui.net
 * [get_dir_files 取得输入目录所包含的所有文件]
 * @param  [type] $dir [目录]
 * @return [type]      [description]
 */
function get_dir_files($dir)
{
    if (is_file($dir)) {
        return array($dir);
    }
    $files = array();
    if (is_dir($dir) && ($dir_p = opendir($dir))) {
        $ds = DIRECTORY_SEPARATOR;
        while (($filename = readdir($dir_p)) !== false) {
            if ($filename=='.' || $filename=='..') { continue; }
            $filetype = filetype($dir.$ds.$filename);
            if ($filetype == 'dir') {
                $files = array_merge($files, self::get_dir_files($dir.$ds.$filename));
            } elseif ($filetype == 'file') {
                $files[] = $dir.$ds.$filename;
            }
        }
        closedir($dir_p);
    }
    return $files;
}

/** fengkui.net
 * [create_dir 创建目录]
 * @param  [type] $dir [目录位置]
 * @return [type]      [description]
 */
function create_dir($dir) 
{
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
    }
}

/** fengkui.net
 * [create_file 创建文件（默认为空）]
 * @param  [type] $filename [文件名称]
 * @return [type]           [description]
 */
function create_file($filename) {
    if (!is_file($filename)) touch($filename);
}

/** fengkui.net
 * [is_writeable 判断 文件/目录 是否可写（取代系统自带的 is_writeable 函数）]
 * @param  [type]  $file [文件/目录]
 * @return boolean       [description]
 */
function is_writeable($file) 
{
    if (is_dir($file)){
        $dir = $file;
        if ($fp = @fopen("$dir/test.txt", 'w')) {
            @fclose($fp);
            @unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    } else {
        if ($fp = @fopen($file, 'a+')) {
            @fclose($fp);
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}

/** fengkui.net
 * [post_upload post上传文件]
 * @param  string $path    [字符串 保存文件路径示例： /Upload/image/]
 * @param  string $format  [文件格式限制]
 * @param  string $maxSize [允许的上传文件最大值 52428800]
 * @return [type]          [返回ajax的json格式数据]
 */
function post_upload($path='file',$format='empty',$maxSize='52428800')
{
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,       //   上传文件最大为50M
                'rootPath'  =>  './',           //文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         //文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     //上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   //  自动使用子目录保存上传文件 默认为true
                'exts'    =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            return $data;
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name']=trim($file['savepath'].$file['savename'],'.');
                return $data;
            }               
        }
    }
}

/** fengkui.net
 * [ajax_upload 上传文件类型控制 此方法仅限ajax上传使用]
 * @param  string $path    [字符串 保存文件路径示例： /Upload/image/]
 * @param  string $format  [文件格式限制]
 * @param  string $maxSize [允许的上传文件最大值 52428800]
 * @return [type]          [返回ajax的json格式数据]
 */
function ajax_upload($path='file',$format='empty',$maxSize='52428800')
{
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,               // 上传文件最大为50M
                'rootPath'  =>  './',                   // 文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         // 文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     // 上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   // 自动使用子目录保存上传文件 默认为true
                'exts'      =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            echo json_encode($data);
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name']=trim($file['savepath'].$file['savename'],'.');
                echo json_encode($data);
            }               
        }
    }
}

/** fengkui.net
 * [upload_success 检测webuploader上传是否成功]
 * @param  [type] $file_path [post中的字段]
 * @return [type]            [description]
 */
function upload_success($file_path)
{
    // 为兼容传进来的有数组；先转成json
    $file_path=json_encode($file_path);
    // 如果有undefined说明上传失败
    if (strpos($file_path, 'undefined') !== false) {
        return false;
    }
    // 如果没有.符号说明上传失败
    if (strpos($file_path, '.') === false) {
        return false;
    }
    // 否则上传成功则返回true
    return true;
}

/** fengkui.net
                        路径或请求地址
 */

/** fengkui.net
 * [path_encode 将路径转换加密]
 * @param  [type] $file_path [路径]
 * @return [type]            [转换后的路径]
 */
function path_encode($file_path)
{
    return rawurlencode(base64_encode($file_path));
}

/** fengkui.net
 * [path_decode 将路径解密]
 * @param  [type] $file_path [加密后的字符串]
 * @return [type]            [解密后的路径]
 */
function path_decode($file_path)
{
    return base64_decode(rawurldecode($file_path));
}

/** fengkui.net
 * [get_url_to_domain 取得根域名]
 * @param  [type] $domain [域名]
 * @return [type]         [返回根域名]
 */
function get_url_to_domain($domain) 
{
    $re_domain = '';
    $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
    $array_domain = explode(".", $domain);
    $array_num = count($array_domain) - 1;
    if ($array_domain[$array_num] == 'cn') {
        if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
            $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
    } else {
        $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
    return $re_domain;
}

/** fengkui.net
 * [api_return ajax返回]
 * @param  [type] $status   [状态]
 * @param  [type] $messages [成功或者错误的提示语]
 * @param  [type] $data     [需要发送到前端的数据]
 * @param  string $next_url [下个url]
 * @param  string $pre_url  [上一个url]
 * @return [type]           [description]
 */
function api_return($status,$messages,$data,$next_url="",$pre_url="")
{
    $request = array(
        'status'    => $status,     //状态
        'messages'  => $messages,   //信息
        'data'      => $data,       //数据
        'next_url'  => $next_url,   //下个url
        'pre_url'   => $pre_url,   //上一个url
        'time'      => time(),      //数据返回时间
    );
    $this->ajax($request);
}

/** fengkui.net
 * [ajax_return 返回iso、Android、ajax的json格式数据]
 * @param  [type] $status   [需要发送到前端的数据]
 * @param  [type] $messages [成功或者错误的提示语]
 * @param  [type] $data     [数据]
 * @param  string $next_url [下个url]
 * @param  string $pre_url  [上一个url]
 * @return [type]           [description]
 */
function ajax_return($status=1,$messages='成功',$data='',$next_url="",$pre_url="")
{
    $all_data=array(
        'status'=>$status,
        'messages'=>$messages,
        'next_url'  => $next_url,   
        'pre_url'   => $pre_url, 
        'time'      => time(),
        );
    if ($data!=='') {
        $all_data['data']=$data;
        // app 禁止使用和为了统一字段做的判断
        $reserved_words=array('id','title','price','product_title','product_id','product_category','product_number');
        foreach ($reserved_words as $k => $v) {
            if (array_key_exists($v, $data)) {
                echo 'app不允许使用【'.$v.'】这个键名 —— 此提示是function.php 中的ajax_return函数返回的';
                die;
            }
        }
    }
    // 如果是ajax或者app访问；则返回json数据 pc访问直接p出来
    echo json_encode($all_data);
    exit(0);
}

/** fengkui.net
 * [httpRequest CURL请求]
 * @param  [type]  $url        [请求url地址]
 * @param  [type]  $method     [请求方法 get post]
 * @param  [type]  $postfields [post数据数组]
 * @param  array   $headers    [请求header信息]
 * @param  boolean $debug      [调试开启 默认false]
 * @return [type]              [description]
 */
function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) 
{
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if($ssl){
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
    //return array($http_code, $response,$requestinfo);
}

/** fengkui.net
 * [parse_url_param 获取url 中的各个参数 类似于 pay_code=alipay&bank_code=ICBC-DEBIT]
 * @param  [type] $str [路径]
 * @return [type]      [description]
 */
function parse_url_param($str)
{
    $data = array();
    $parameter = explode('&',end(explode('?',$str)));
    foreach($parameter as $val){
        $tmp = explode('=',$val);
        $data[$tmp[0]] = $tmp[1];
    }
    return $data;
}

/** fengkui.net
                                时间操作
 */
/** fengkui.net
 * [YMDDate 格式化时间]
 * @param  string $time [时间]
 * @return [type]       [description]
 */
function YMDDate($time='default') 
{
    $date = $time == 'default' ? date('Y-m-d H:i:s', time()) : date('Y-m-d H:i:s', $time);
    return $date;
}

/** fengkui.net
 * [friend_date 友好时间显示]
 * @param  [type] $time [时间]
 * @return [type]       [description]
 */
function friend_date($time)
{
    if (!$time)
        return false;
    $fdate = '';
    $d = time() - intval($time);
    $ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}

/** fengkui.net
 * [get_zodiac_sign 计算星座的函数 string get_zodiac_sign(string month, string day)]
 * @param  [type] $month [月份]
 * @param  [type] $day   [日期]
 * @return [type]        [输出：星座名称或者错误信息]
 */
function get_zodiac_sign($month, $day)
{
    // 检查参数有效性
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
        return (false);
        // 星座名称以及开始日期
        $signs = array(
        array( "20" => "水瓶座"),
        array( "19" => "双鱼座"),
        array( "21" => "白羊座"),
        array( "20" => "金牛座"),
        array( "21" => "双子座"),
        array( "22" => "巨蟹座"),
        array( "23" => "狮子座"),
        array( "23" => "处女座"),
        array( "23" => "天秤座"),
        array( "24" => "天蝎座"),
        array( "22" => "射手座"),
        array( "22" => "摩羯座")
    );
    list($sign_start, $sign_name) = each($signs[(int)$month-1]);
    if ($day < $sign_start)
    list($sign_start, $sign_name) = each($signs[($month -2 < 0) ? $month = 11: $month -= 2]);
    return $sign_name;
}

/** fengkui.net
 * [word_time 传入时间戳,计算距离现在的时间]
 * @param  [type] $time [时间戳]
 * @return [type]       [返回多少以前]
 */
function word_time($time) 
{
    $time = (int) substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2){
        $str = sprintf('刚刚', $int);
    }elseif ($int < 60){
        $str = sprintf('%d秒前', $int);
    }elseif ($int < 3600){
        $str = sprintf('%d分钟前', floor($int / 60));
    }elseif ($int < 86400){
        $str = sprintf('%d小时前', floor($int / 3600));
    }elseif ($int < 1728000){
        $str = sprintf('%d天前', floor($int / 86400));
    }else{
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}

/** fengkui.net
                            图片视频操作
 */

/** fengkui.net
 * [app_upload_image app 图片上传]
 * @param  [type]  $path    [上传图图片的路径]
 * @param  integer $maxSize [上传图片的大小控制]
 * @return [type]           [上传后的图片名]
 */
function app_upload_image($path,$maxSize=52428800)
{
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'.');
    $path=trim($path,'/');
    $config=array(
        'rootPath'  =>'./',         //文件上传保存的根路径
        'savePath'  =>'./'.$path.'/',   
        'exts'      => array('jpg', 'gif', 'png', 'jpeg','bmp'),
        'maxSize'   => $maxSize,
        'autoSub'   => true,
        );
    $upload = new \Think\Upload($config);// 实例化上传类
    $info = $upload->upload();
    if($info) {
        foreach ($info as $k => $v) {
            $data[]=trim($v['savepath'],'.').$v['savename'];
        }
        return $data;
    }
}

/** fengkui.net
 * [app_upload_video 视频上传]
 * @param  [type]  $path    [路径]
 * @param  integer $maxSize [上传后的视频名]
 * @return [type]           [description]
 */
function app_upload_video($path,$maxSize=52428800)
{
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'.');
    $path=trim($path,'/');
    $config=array(
        'rootPath'  =>'./',         //文件上传保存的根路径
        'savePath'  =>'./'.$path.'/',   
        'exts'      => array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack'),
        'maxSize'   => $maxSize,
        'autoSub'   => true,
        );
    $upload = new \Think\Upload($config);// 实例化上传类
    $info = $upload->upload();
    if($info) {
        foreach ($info as $k => $v) {
            $data[]=trim($v['savepath'],'.').$v['savename'];
        }
        return $data;
    }
}

/** fengkui.net
 * [resize_img 图片等比例缩放]
 * @param  [type] $im        [新建图片资源(imagecreatefromjpeg/imagecreatefrompng/imagecreatefromgif)]
 * @param  [type] $maxwidth  [生成图像宽]
 * @param  [type] $maxheight [生成图像高]
 * @param  [type] $name      [生成图像名称]
 * @param  [type] $filetype  [文件类型(.jpg/.gif/.png)]
 * @return [type]            [description]
 */
function resize_img($im, $maxwidth, $maxheight, $name, $filetype) 
{
    $pic_width = imagesx($im);
    $pic_height = imagesy($im);
    if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
        if($maxwidth && $pic_width>$maxwidth) {
            $widthratio = $maxwidth/$pic_width;
            $resizewidth_tag = true;
        }
        if($maxheight && $pic_height>$maxheight) {
            $heightratio = $maxheight/$pic_height;
            $resizeheight_tag = true;
        }
        if($resizewidth_tag && $resizeheight_tag) {
            if($widthratio<$heightratio)
                $ratio = $widthratio;
            else
                $ratio = $heightratio;
        }
        if($resizewidth_tag && !$resizeheight_tag)
            $ratio = $widthratio;
        if($resizeheight_tag && !$resizewidth_tag)
            $ratio = $heightratio;
        $newwidth = $pic_width * $ratio;
        $newheight = $pic_height * $ratio;
        if(function_exists("imagecopyresampled")) {
            $newim = imagecreatetruecolor($newwidth,$newheight);
            imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
        } else {
            $newim = imagecreate($newwidth,$newheight);
            imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
        }
        $name = $name.$filetype;
        imagejpeg($newim,$name);
        imagedestroy($newim);
    } else {
        $name = $name.$filetype;
        imagejpeg($im,$name);
    }
}

/** fengkui.net
 * [crop_image 生成缩略图]
 * @param  [type]  $image_path [原图path]
 * @param  integer $width      [缩略图的宽]
 * @param  integer $height     [缩略图的高]
 * @return [type]              [缩略图path]
 */
function crop_image($image_path,$width=170,$height=170)
{
    $image_path=trim($image_path,'.');
    $min_path='.'.str_replace('.', '_'.$width.'_'.$height.'.', $image_path);
    $image = new \Think\Image();
    $image->open($image_path);
    // 生成一个居中裁剪为$width*$height的缩略图并保存
    $image->thumb($width, $height,\Think\Image::IMAGE_THUMB_CENTER)->save($min_path);
    oss_upload($min_path);
    return $min_path;
}

/** fengkui.net
 * [add_mark 给已经存在的图片添加水印]
 * @param [type] $file_path [图片路径]
 */
function add_mark($file_path) 
{
    if (file_exists($file_path) && file_exists(MARK)) {
        //求出上传图片的名称后缀
        $ext_name = strtolower(substr($file_path, strrpos($file_path, '.'), strlen($file_path)));
        //$new_name='jzy_' . time() . rand(1000,9999) . $ext_name ;
        $store_path = ROOT_PATH . UPDIR;
        //求上传图片高宽
        $imginfo = getimagesize($file_path);
        $width = $imginfo[0];
        $height = $imginfo[1];
        //添加图片水印
        switch($ext_name) {
            case '.gif':
                $dst_im = imagecreatefromgif($file_path);
                break;
            case '.jpg':
                $dst_im = imagecreatefromjpeg($file_path);
                break;
            case '.png':
                $dst_im = imagecreatefrompng($file_path);
                break;
        }
        $src_im = imagecreatefrompng(MARK);
        //求水印图片高宽
        $src_imginfo = getimagesize(MARK);
        $src_width = $src_imginfo[0];
        $src_height = $src_imginfo[1];
        //求出水印图片的实际生成位置
        $src_x = $width - $src_width - 10;
        $src_y = $height - $src_height - 10;
        //新建一个真彩色图像
        $nimage = imagecreatetruecolor($width, $height);
        //拷贝上传图片到真彩图像
        imagecopy($nimage, $dst_im, 0, 0, 0, 0, $width, $height);
        //按坐标位置拷贝水印图片到真彩图像上
        imagecopy($nimage, $src_im, $src_x, $src_y, 0, 0, $src_width, $src_height);
        //分情况输出生成后的水印图片
        switch($ext_name) {
            case '.gif':
                imagegif($nimage, $file_path);
                break;
            case '.jpg':
                imagejpeg($nimage, $file_path);
                break;
            case '.png':
                imagepng($nimage, $file_path);
                break;
        }
        //释放资源
        imagedestroy($dst_im);
        imagedestroy($src_im);
        unset($imginfo);
        unset($src_imginfo);
        //移动生成后的图片
        @move_uploaded_file($file_path, ROOT_PATH.UPDIR . $file_path);
    }
}

/** fengkui.net
 * [downloadImage 下载远程图片]
 * @param  [type] $url      [图片的绝对url]
 * @param  [type] $filepath [文件的完整路径（例如/www/images/test），此函数会自动根据图片url和http头信息确定图片的后缀名]
 * @param  [type] $filename [要保存的文件名(不含扩展名)]
 * @return [type]           [下载成功返回一个描述图片信息的数组，下载失败则返回false]
 */
function downloadImage($url, $filepath, $filename) 
{
    //服务器返回的头信息
    $responseHeaders = array();
    //原始图片名
    $originalfilename = '';
    //图片的后缀名
    $ext = '';
    $ch = curl_init($url);
    //设置curl_exec返回的值包含Http头
    curl_setopt($ch, CURLOPT_HEADER, 1);
    //设置curl_exec返回的值包含Http内容
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //设置抓取跳转（http 301，302）后的页面
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //设置最多的HTTP重定向的数量
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    //服务器返回的数据（包括http头信息和内容）
    $html = curl_exec($ch);
    //获取此次抓取的相关信息
    $httpinfo = curl_getinfo($ch);
    curl_close($ch);
    if ($html !== false) {
        //分离response的header和body，由于服务器可能使用了302跳转，所以此处需要将字符串分离为 2+跳转次数 个子串
        $httpArr = explode("\r\n\r\n", $html, 2 + $httpinfo['redirect_count']);
        //倒数第二段是服务器最后一次response的http头
        $header = $httpArr[count($httpArr) - 2];
        //倒数第一段是服务器最后一次response的内容
        $body = $httpArr[count($httpArr) - 1];
        $header.="\r\n";
        //获取最后一次response的header信息
        preg_match_all('/([a-z0-9-_]+):\s*([^\r\n]+)\r\n/i', $header, $matches);
        if (!empty($matches) && count($matches) == 3 && !empty($matches[1]) && !empty($matches[1])) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                if (array_key_exists($i, $matches[2])) {
                    $responseHeaders[$matches[1][$i]] = $matches[2][$i];
                }
            }
        }
        //获取图片后缀名
        if (0 < preg_match('{(?:[^\/\\\\]+)\.(jpg|jpeg|gif|png|bmp)$}i', $url, $matches)) {
            $originalfilename = $matches[0];
            $ext = $matches[1];
        } else {
            if (array_key_exists('Content-Type', $responseHeaders)) {
                if (0 < preg_match('{image/(\w+)}i', $responseHeaders['Content-Type'], $extmatches)) {
                    $ext = $extmatches[1];
                }
            }
        }
        //保存文件
        if (!empty($ext)) {
            //如果目录不存在，则先要创建目录
            if(!is_dir($filepath)){
                mkdir($filepath, 0777, true);
            }
            $filepath .= '/'.$filename.".$ext";
            $local_file = fopen($filepath, 'w');
            if (false !== $local_file) {
                if (false !== fwrite($local_file, $body)) {
                    fclose($local_file);
                    $sizeinfo = getimagesize($filepath);
                    return array('filepath' => realpath($filepath), 'width' => $sizeinfo[0], 'height' => $sizeinfo[1], 'orginalfilename' => $originalfilename, 'filename' => pathinfo($filepath, PATHINFO_BASENAME));
                }
            }
        }
    }
    return false;
}

/** fengkui.net
                                JS相关函数
 */
/** fengkui.net
 * [alertLocation js 弹窗并且跳转]
 * @param  [type] $_info [弹窗内容]
 * @param  [type] $_url  [跳转地址]
 * @return [type]        [description]
 */
function alertLocation($_info, $_url) 
{
    echo "<script type='text/javascript'>alert('$_info');location.href='$_url';</script>";
    exit();
}

/** fengkui.net
 * [alertBack js 弹窗返回]
 * @param  [type] $_info [弹窗内容]
 * @return [type]        [description]
 */
function alertBack($_info) 
{
    echo "<script type='text/javascript'>alert('$_info');history.back();</script>";
    exit();
}

/** fengkui.net
 * [headerUrl 页面跳转]
 * @param  [type] $url [跳转地址]
 * @return [type]      [description]
 */
function headerUrl($url) 
{
    echo "<script type='text/javascript'>location.href='{$url}';</script>";
    exit();
}

/** fengkui.net
 * [alertClose 弹窗关闭]
 * @param  [type] $_info [弹窗内容]
 * @return [type]        [description]
 */
function alertClose($_info) 
{
    echo "<script type='text/javascript'>alert('$_info');close();</script>";
    exit();
}

/** fengkui.net
 * [alert 弹窗]
 * @param  [type] $_info [弹窗内容]
 * @return [type]        [description]
 */
function alert($_info) 
{
    echo "<script type='text/javascript'>alert('$_info');</script>";
    exit();
}
/** fengkui.net
                        Thinkphp和其他接口
 */

/** fengkui.net
 * [sensitive 判断用户输入是否存在敏感词]
 * 需要在ThinkPHP的ORG扩展文件夹中，添加敏感词类文件SensitiveFilter.php
 * @param  [type] $content [文本内容]
 * @return [type]          [description]
 */
function sensitive($content){
    //$arr=C('SENSITIVE');
    import("ORG.SensitiveFilter");
    $arr=SensitiveFilter::getWord();
    foreach ($arr as $v) {
        if (false !== strstr($content, $v)){
            $content=str_replace($v,'***',$content);//内容中存在敏感词库中的敏感词，则将敏感词用*替换
        }
    }
    return $content;
}

/** fengkui.net
 * [show_verify 设置验证码，生成验证码字符串]
 * @param  string $config [thinkphp自带验证码生成类]
 * @return [type]         [返回生成的验证码字符串]
 */
function show_verify($config='')
{
    if($config==''){
        $config=array(
            'codeSet'=>'1234567890',
            'fontSize'=>30,
            'useCurve'=>false,
            'imageH'=>60,
            'imageW'=>240,
            'length'=>4,
            'fontttf'=>'4.ttf',
            );
    }
    $verify=new \Think\Verify($config);
    return $verify->entry();
}

/** fengkui.net
 * [check_verify thinkphp自带验证码检测功能]
 * @param  [type] $code [验证码]
 * @return [type]       [description]
 */
function check_verify($code)
{
    $verify=new \Think\Verify();
    return $verify->check($code);
}

/** fengkui.net
 * [new_page 实例化page类]
 * @param  [type]  $count [$count 总数]
 * @param  integer $limit [每页数量]
 * @return [type]         [page类]
 */
function new_page($count,$limit=10)
{
    return new \Org\Nx\Page($count,$limit);
}

/** fengkui.net
 * [get_page_data 获取分页数据]
 * @param  [type]  $model [model对象]
 * @param  [type]  $map   [where条件]
 * @param  string  $order [排序规则]
 * @param  integer $limit [每页数量]
 * @return [type]         [分页数据]
 */
function get_page_data($model,$map,$order='',$limit=10)
{
    $count=$model
        ->where($map)
        ->count();
    $page=new_page($count,$limit);
    // 获取分页数据
    $list=$model
            ->where($map)
            ->order($order)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
    $data=array(
        'data'=>$list,
        'page'=>$page->show()
        );
    return $data;
}

/** fengkui.net
 * [alipay 跳向支付宝付款]
 * @param  [type] $order [订单数据 必须包含 out_trade_no(订单号)、price(订单金额)、subject(商品名称标题)]
 * @return [type]        [description]
 */
function alipay($order)
{
    vendor('Alipay.AlipaySubmit','','.class.php');
    // 获取配置
    $config=C('ALIPAY_CONFIG');
    $data=array(
        "_input_charset" => $config['input_charset'], // 编码格式
        "logistics_fee" => "0.00", // 物流费用
        "logistics_payment" => "SELLER_PAY", // 物流支付方式SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        "logistics_type" => "EXPRESS", // 物流类型EXPRESS（快递）、POST（平邮）、EMS（EMS）
        "notify_url" => $config['notify_url'], // 异步接收支付状态通知的链接
        "out_trade_no" => $order['out_trade_no'], // 订单号
        "partner" => $config['partner'], // partner 从支付宝商户版个人中心获取
        "payment_type" => "1", // 支付类型对应请求时的 payment_type 参数,原样返回。固定设置为1即可
        "price" => $order['price'], // 订单价格单位为元
        // "price" => 0.01, // // 调价用于测试
        "quantity" => "1", // price、quantity 能代替 total_fee。 即存在 total_fee,就不能存在 price 和 quantity;存在 price、quantity, 就不能存在 total_fee。 （没绕明白；好吧；那无视这个参数即可）
        "receive_address" => '1', // 收货人地址 即时到账方式无视此参数即可
        "receive_mobile" => '1', // 收货人手机号码 即时到账方式无视即可
        "receive_name" => '1', // 收货人姓名 即时到账方式无视即可
        "receive_zip" => '1', // 收货人邮编 即时到账方式无视即可
        "return_url" => $config['return_url'], // 页面跳转 同步通知 页面路径 支付宝处理完请求后,当前页面自 动跳转到商户网站里指定页面的 http 路径。
        "seller_email" => $config['seller_email'], // email 从支付宝商户版个人中心获取
        "service" => "create_direct_pay_by_user", // 接口名称 固定设置为create_direct_pay_by_user
        "show_url" => $config['show_url'], // 商品展示网址,收银台页面上,商品展示的超链接。
        "subject" => $order['subject'] // 商品名称商品的标题/交易标题/订单标 题/订单关键字等
    );
    $alipay=new \AlipaySubmit($config);
    $new=$alipay->buildRequestPara($data);
    $go_pay=$alipay->buildRequestForm($new, 'get','支付');
    echo $go_pay;
}

/** fengkui.net
 * [weixinpay 微信扫码支付]
 * @param  [type] $order [订单 必须包含支付所需要的参数 body(产品描述)、total_fee(订单金额)、out_trade_no(订单号)、product_id(产品id)]
 * @return [type]        [description]
 */
function weixinpay($order)
{
    $order['trade_type']='NATIVE';
    Vendor('Weixinpay.Weixinpay');
    $weixinpay=new \Weixinpay();
    $weixinpay->pay($order);
}

/** fengkui.net
 * [geetest_chcek_verify geetest检测验证码]
 * @param  [type] $data [字符]
 * @return [type]       [description]
 */
function geetest_chcek_verify($data)
{
    $geetest_id=C('GEETEST_ID');
    $geetest_key=C('GEETEST_KEY');
    $geetest=new \Org\Xb\Geetest($geetest_id,$geetest_key);
    $user_id=$_SESSION['geetest']['user_id'];
    if ($_SESSION['geetest']['gtserver']==1) {
        $result=$geetest->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $user_id);
        if ($result) {
            return true;
        } else{
            return false;
        }
    }else{
        if ($geetest->fail_validate($data['geetest_challenge'],$data['geetest_validate'],$data['geetest_seccode'])) {
            return true;
        }else{
            return false;
        }
    }
}

/** fengkui.net
 * [get_rong_key_secret 根据配置项获取对应的key和secret]
 * @return [type] [key和secret]
 */
function get_rong_key_secret()
{
    // 判断是需要开发环境还是生产环境的key
    if (C('RONG_IS_DEV')) {
        $key=C('RONG_DEV_APP_KEY');
        $secret=C('RONG_DEV_APP_SECRET');
    }else{
        $key=C('RONG_PRO_APP_KEY');
        $secret=C('RONG_PRO_APP_SECRET');
    }
    $data=array(
        'key'=>$key,
        'secret'=>$secret
        );
    return $data;
}

/** fengkui.net
 * [get_rongcloud_token 获取融云token]
 * @param  [type] $uid [用户id]
 * @return [type]      [token]
 */
function get_rongcloud_token($uid)
{
    // 从数据库中获取token
    $token=D('OauthUser')->getToken($uid,1);
    // 如果有token就返回
    if ($token) {
        return $token;
    }
    // 获取用户昵称和头像
    $user_data=M('Users')->field('username,avatar')->getById($uid);
    // 用户不存在
    if (empty($user_data)) {
        return false;
    }
    // 获取头像url格式
    $avatar=get_url($user_data['avatar']);
    // 获取key和secret
    $key_secret=get_rong_key_secret();
    // 实例化融云
    $rong_cloud=new \Org\Xb\RongCloud($key_secret['key'],$key_secret['secret']);
    // 获取token
    $token_json=$rong_cloud->getToken($uid,$user_data['username'],$avatar);
    $token_array=json_decode($token_json,true);
    // 获取token失败
    if ($token_array['code']!=200) {
        return false;
    }
    $token=$token_array['token'];
    $data=array(
        'uid'=>$uid,
        'type'=>1,
        'nickname'=>$user_data['username'],
        'head_img'=>$avatar,
        'access_token'=>$token
        );
    // 插入数据库
    $result=D('OauthUser')->addData($data);
    if ($result) {
        return $token;
    }else{
        return false;
    }
}

/** fengkui.net
 * [refresh_rongcloud_token 更新融云头像]
 * @param  [type] $uid [用户id]
 * @return [type]      [操作是否成功]
 */
function refresh_rongcloud_token($uid)
{
    // 获取用户昵称和头像
    $user_data=M('Users')->field('username,avatar')->getById($uid);
    // 用户不存在
    if (empty($user_data)) {
        return false;
    }
    $avatar=get_url($user_data['avatar']);
    // 获取key和secret
    $key_secret=get_rong_key_secret();
    // 实例化融云
    $rong_cloud=new \Org\Xb\RongCloud($key_secret['key'],$key_secret['secret']);
    // 更新融云用户头像
    $result_json=$rong_cloud->userRefresh($uid,$user_data['username'],$avatar);
    $result_array=json_decode($result_json,true);
    if ($result_array['code']==200) {
        return true;
    }else{
        return false;
    }
}

/** fengkui.net
 * [send_sms_code 发送 容联云通讯 验证码]
 * @param  [type] $phone [手机号]
 * @param  [type] $code  [验证码]
 * @return [type]        [是否发送成功]
 */
function send_sms_code($phone,$code)
{
    //请求地址，格式如下，不需要写https://
    $serverIP='app.cloopen.com';
    //请求端口
    $serverPort='8883';
    //REST版本号
    $softVersion='2013-12-26';
    //主帐号
    $accountSid=C('RONGLIAN_ACCOUNT_SID');
    //主帐号Token
    $accountToken=C('RONGLIAN_ACCOUNT_TOKEN');
    //应用Id
    $appId=C('RONGLIAN_APPID');
    //应用Id
    $templateId=C('RONGLIAN_TEMPLATE_ID');
    $rest = new \Org\Xb\Rest($serverIP,$serverPort,$softVersion);
    $rest->setAccount($accountSid,$accountToken);
    $rest->setAppId($appId);
    // 发送模板短信
    $result=$rest->sendTemplateSMS($phone,array($code,5),$templateId);
    if($result==NULL) {
        return false;
    }
    if($result->statusCode!=0) {
        return  false;
    }else{
        return true;
    }
}

/** fengkui.net
 * [send_email 发送邮件]
 * @param  [type] $address [需要发送的邮箱地址 发送给多个地址需要写成数组形式]
 * @param  [type] $subject [标题]
 * @param  [type] $content [内容]
 * @return [type]          [是否成功]
 */
function send_email($address,$subject,$content)
{
    $email_smtp=C('EMAIL_SMTP');
    $email_username=C('EMAIL_USERNAME');
    $email_password=C('EMAIL_PASSWORD');
    $email_from_name=C('EMAIL_FROM_NAME');
    if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
        return array("error"=>1,"message"=>'邮箱配置不完整');
    }
    require './ThinkPHP/Library/Org/Nx/class.phpmailer.php';
    require './ThinkPHP/Library/Org/Nx/class.smtp.php';
    $phpmailer=new \Phpmailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 设置为html格式
    $phpmailer->IsHTML(true);
    // 设置邮件的字符编码'
    $phpmailer->CharSet='UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host=$email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth=true;
    // 设置用户名
    $phpmailer->Username=$email_username;
    // 设置密码
    $phpmailer->Password=$email_password;
    // 设置邮件头的From字段。
    $phpmailer->From=$email_username;
    // 设置发件人名字
    $phpmailer->FromName=$email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    if(is_array($address)){
        foreach($address as $addressv){
            $phpmailer->AddAddress($addressv);
        }
    }else{
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject=$subject;
    // 设置邮件正文
    $phpmailer->Body=$content;
    // 发送邮件。
    if(!$phpmailer->Send()) {
        $phpmailererror=$phpmailer->ErrorInfo;
        return array("error"=>1,"message"=>$phpmailererror);
    }else{
        return array("error"=>0);
    }
}

/** fengkui.net
 * [umeng_push 发送友盟推送消息]
 * @param  [type] $uid   [用户id]
 * @param  [type] $title [推送的标题]
 * @return [type]        [是否成功]
 */
function umeng_push($uid,$title)
{
    // 获取token
    $device_tokens=D('OauthUser')->getToken($uid,2);
    // 如果没有token说明移动端没有登录；则不发送通知
    if (empty($device_tokens)) {
        return false;
    }
    // 导入友盟
    Vendor('Umeng.Umeng');
    // 自定义字段   根据实际环境分配；如果不用可以忽略
    $status=1;
    // 消息未读总数统计  根据实际环境获取未读的消息总数 此数量会显示在app图标右上角
    $count_number=1;
    $data=array(
        'key'=>'status',
        'value'=>"$status",
        'count_number'=>$count_number
        );
    // 判断device_token  64位表示为苹果 否则为安卓
    if(strlen($device_tokens)==64){
        $key=C('UMENG_IOS_APP_KEY');
        $timestamp=C('UMENG_IOS_SECRET');
        $umeng=new \Umeng($key, $timestamp);
        $umeng->sendIOSUnicast($data,$title,$device_tokens);
    }else{
        $key=C('UMENG_ANDROID_APP_KEY');
        $timestamp=C('UMENG_ANDROID_SECRET');
        $umeng=new \Umeng($key, $timestamp);
        $umeng->sendAndroidUnicast($data,$title,$device_tokens);
    }
    return true;
}

/** fengkui.net
 * [new_oss 阿里云OSS操作 实例化阿里云oos]
 * @return [type] [实例化得到的对象]
 */
function new_oss()
{
    vendor('Alioss.autoload');
    $config=C('ALIOSS_CONFIG');
    $oss=new \OSS\OssClient($config['KEY_ID'],$config['KEY_SECRET'],$config['END_POINT']);
    return $oss;
}

/** fengkui.net
 * [uploadObject 阿里云OSS操作  上传Object]
 * @param  [type] $str [是要上传的专题的内容]
 * @param  [type] $id  [用专题的id标识本片专题]
 * @return [type]      [description]
 */
function uploadObject($str,$id)
{
    $id='M_Upload/zhuanti/content/'.$id;
    $accessKeyId=C('ALIOSS_CONFIG.KEY_ID');
    $accessKeySecret=C('ALIOSS_CONFIG.KEY_SECRET');
    $endpoint=C('ALIOSS_CONFIG.END_POINT');
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    //$oss->putObject($bucket,$id,$str);
    vendor('Alioss.autoload');
    $config=C('ALIOSS_CONFIG');
    $ossClient=new \OSS\OssClient($config['KEY_ID'],$config['KEY_SECRET'],$config['END_POINT']);
    try {
        
        //$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $ossClient->putObject($bucket, $id, $str);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    return $id;
}

/** fengkui.net
 * [downObject 阿里云OSS操作 上传Object]
 * @param  [type] $id [用专题的id标识本片专题]
 * @return [type]     [description]
 */
function downObject($id)
{
    $accessKeyId=C('ALIOSS_CONFIG.KEY_ID');
    $accessKeySecret=C('ALIOSS_CONFIG.KEY_SECRET');
    $endpoint=C('ALIOSS_CONFIG.END_POINT');
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    //$oss->putObject($bucket,$id,$str);
    try {
        vendor('Alioss.autoload');
        $config=C('ALIOSS_CONFIG');
        $ossClient=new \OSS\OssClient($config['KEY_ID'],$config['KEY_SECRET'],$config['END_POINT']);
        $content=$ossClient->getObject($bucket, $id);
        print("object content: " . $content);
    } catch (OssException $e) {
        print $e->getMessage();
    }
}

/** fengkui.net
 * [oss_upload 阿里云OSS操作 上传文件到阿里云OSS并删除本地文件]
 * @param  [type] $path [文件路径]
 * @return [type]       [是否上传]
 */
function oss_upload($path)
{
    // 获取bucket名称
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    // 先统一去除左侧的.或者/ 再添加./
    $oss_path=ltrim($path,'./');
    $path='./'.$oss_path;
    if (file_exists($path)) {
        // 实例化oss类
        $oss=new_oss();
        // 上传到oss    
        $oss->uploadFile($bucket,$oss_path,$path);
        // 如需上传到oss后 自动删除本地的文件 则删除下面的注释 
        unlink($path);
        return true;
    }
    return false;
}

/** fengkui.net
 * [oss_delet_object 阿里云OSS操作 删除阿里云OSS上指定文件]
 * @param  [type] $object [文件路径 例如删除 /Public/README.md文件  传Public/README.md 即可]
 * @return [type]         [description]
 */
function oss_delet_object($object)
{
    // 实例化oss类
    $oss=new_oss();
    // 获取bucket名称
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    $test=$oss->deleteObject($bucket,$object);
}

/** fengkui.net
 * [get_url 阿里云OSS操作 获取完整网络连接]
 * @param  [type] $path [文件路径]
 * @return [type]       [http连接]
 */
function get_url($path)
{
    // 如果是空；返回空
    if (empty($path)) {
        return '';
    }
    // 如果已经有http直接返回
    if (strpos($path, 'http://')!==false) {
        return $path;
    }
    // 判断是否使用了oss
    $alioss=C('ALIOSS_CONFIG');
    if (empty($alioss['KEY_ID'])) {
        return 'http://'.$_SERVER['HTTP_HOST'].$path;
    }else{
        $path=ltrim($path,'.');
        return 'http://'.$alioss['BUCKET'].'.'.$alioss['END_POINT'].$path;
    } 
}
