<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2022-03-16T15:23:56+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2021-12-16T15:23:56+08:00
 */
namespace feng;

/**
 * 图片处理
 */
class Canvas
{
    /**
     * [getThumb 压缩图片]
     * @param  [type] $filestring [图片远程地址]
     * @param  [type] $dstwidth   [宽]
     * @param  [type] $dstheight  [高]
     * @param  [type] $type       [用1表示留白方式处理，2表示截取方式处理]
     * @return [type]             [description]
     */
    public static function get_thumb($url, $dstwidth, $dstheight, $type)
    {
        $srcimg=0;
        $dstx=$dsty=$srcx=$srcy=0;

        $yuan = @file_get_contents($url);
        $srcimg = imagecreatefromstring($yuan);

        $src_w = imagesx($srcimg);
        $src_h = imagesy($srcimg);
        $dstwidth = $dstwidth > 0 ? $dstwidth : $src_w;
        $dstheight = $dstheight > 0 ? $dstheight : $src_h;
        $canvas = imagecreatetruecolor($dstwidth, $dstheight);
        if($dstwidth/$dstheight < $src_w/$src_h) { //传来的图像是横图
            if($type==1) { //用户需要留白处理
                $dsty=(int)($dstheight-$src_h*$dstwidth/$src_w)/2;
                $dstheight=$dstheight-2*$dsty;
            } else { //用户需要截取处理
                $srcx=(int)($src_w-$dstwidth*$src_h/$dstheight)/2;
                $src_w=$src_w-2*$srcx;
            }
        } else { //传来的图像是竖图或等比的图
            if($type==1) { //用户需要留白处理
                $dstx=(int)($dstwidth-$src_w*$dstheight/$src_h)/2;
                $dstwidth=$dstwidth-2*$dstx;
            } else { //用户需要截取处理
                // $srcy=(int)($src_h-$dstheight*$src_w/$dstwidth)/2;
                $src_h=$dstheight*$src_w/$dstwidth;
            }
        }
        imagecopyresampled($canvas,$srcimg,$dstx,$dsty,$srcx,$srcy,$dstwidth,$dstheight,$src_w,$src_h);
        return $canvas;
    }

    /**
     * [yuan_img 剪切图片为圆形]
     * @param  [type] $picture [图片数据流 比如file_get_contents(imageurl)返回的数据]
     * @return [type]          [图片数据流]
     */
    public static function yuan_img($picture)
    {
        $src_img = imagecreatefromstring($picture);
        $w = imagesx($src_img);
        $h = imagesy($src_img);
        $w = min($w, $h);
        $h = $w;
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, false); // 设定图像的混色模式
        imagesavealpha($img, true); // 这一句一定要有（设置标记以在保存 PNG 图像时保存完整的 alpha 通道信息）
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127); // 拾取一个完全透明的颜色,最后一个参数127为全透明
        imagefill($img, 0, 0, $bg);
        $r = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        /**
         * 如果想要直接输出图片，应该先设header。header("Content-Type: image/png; charset=utf-8");
         * 并且去掉缓存区函数
         */
        //获取输出缓存，否则imagepng会把图片输出到浏览器
        ob_start();
        imagealphablending($img, false); // (很重要)不合并颜色,直接用 PNG 图像颜色替换,包括透明色;
        imagesavealpha($img, true);  // (很重要)设置标记以在保存 PNG 图像时保存完整的 alpha 通道信息;
        imagepng($img);
        imagedestroy($img);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * [qrcode_with_logo 在二维码的中间区域镶嵌图片]
     * @param  [type] $qrcode [二维码数据流。比如file_get_contents(imageurl)返回的数据,或者微信给返回的数据]
     * @param  [type] $logo   [中间显示图片的数据流。比如file_get_contents(imageurl)返回的东东]
     * @return [type]         [返回图片数据流]
     */
    public static function qrcode_with_logo($qrcode, $logo)
    {
        $qrcode = imagecreatefromstring($qrcode);
        $logo = imagecreatefromstring($logo);
        $qrcode_width = imagesx($qrcode); // 二维码图片宽度
        $qrcode_height = imagesy($qrcode); // 二维码图片高度
        $logo_width = imagesx($logo); // logo图片宽度
        $logo_height = imagesy($logo); // logo图片高度
        $logo_qr_width = $qrcode_width / 2.2; // 组合之后logo的宽度(占二维码的1/2.2)
        $scale = $logo_width / $logo_qr_width; // logo的宽度缩放比(本身宽度/组合后的宽度)
        $logo_qr_height = $logo_height / $scale; // 组合之后logo的高度
        $from_width = ($qrcode_width - $logo_qr_width) / 2; // 组合之后logo左上角所在坐标点
        /**
         * 重新组合图片并调整大小
         * imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
         */
        imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        /**
         * 如果想要直接输出图片，应该先设header。header("Content-Type: image/png; charset=utf-8");
         * 并且去掉缓存区函数
         */
        //获取输出缓存，否则imagepng会把图片输出到浏览器
        ob_start();
        imagealphablending($qrcode, false); // (很重要)不合并颜色,直接用 PNG 图像颜色替换,包括透明色;
        imagesavealpha($qrcode, true);  // (很重要)设置标记以在保存 PNG 图像时保存完整的 alpha 通道信息;
        imagepng($qrcode);
        imagedestroy($qrcode);
        imagedestroy($logo);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * [image_line 画直线]
     * @param  [type]  $canvas  [画布]
     * @param  [type]  $color   [颜色]
     * @param  [type]  $start_x [起点X坐标]
     * @param  [type]  $start_y [起点Y坐标]
     * @param  [type]  $end_x   [终点X坐标]
     * @param  [type]  $end_y   [终点Y坐标]
     * @param  integer $num     [重复次数]
     * @param  string  $type    [重复方向]
     * @return [type]           [description]
     */
    public function image_line($canvas, $color, $start_x, $start_y, $end_x, $end_y, $num = 1, $type="")
    {
        // 设置字体颜色（十六进制颜色码转换RGB颜色值与透明度）
        $color = self::color_16($color);
        $color = imagecolorallocatealpha($canvas, $color[0], $color[1], $color[2], $color[3]);
        for ($i=0; $i < $num; $i++) {
            if ($type == 'x') {
                $start_x += $i;
                $end_x += $i;
            } else {
                $start_y += $i;
                $end_y += $i;
            }
            // 画线
            imageline($canvas, $start_x, $start_y, $end_x, $end_y, $color);
        }
        return $canvas;
    }

    /**
     * [multiline_writer 多行字体写入画布]
     * @param  [type] $canvas     [画布]
     * @param  [type] $text       [字体]
     * @param  [type] $color      [颜色]
     * @param  [type] $text_size  [大小]
     * @param  [type] $text_fonts [样式]
     * @param  [type] $text_x     [起始点X]
     * @param  [type] $text_y     [起始点Y]
     * @param  [type] $length     [每行字体限制字节长度]
     * @param  [type] $width      [每行字体间距]
     * @return [type]             [description]
     */
    public function multiline_writer($canvas, $text, $color, $text_size, $text_fonts, $text_x, $text_y, $length, $width)
    {
        $textNum = mb_strlen($text);
        $text = '';
        $hangshu = 0;

        for ($i=0; $i < $textNum; $i++) {
            $text .= mb_substr($text, $i, 1);
            if (strlen($text)  > $length) {
                $textY = $text_y + $width * $hangshu;
                self::text_writer($canvas, $text, $color, $text_size, $text_fonts, $text_x, $textY); // 字体写入
                $text = '';
                $hangshu ++;
            }
            if (strlen($text) <= $length && $i == $textNum-1) {
                $textY = $text_y + $width * $hangshu;
                self::text_writer($canvas, $text, $color, $text_size, $text_fonts, $text_x, $textY); // 字体写入
            }
        }
        return $canvas;
    }

    /**
     * [text_writer 画布写入字体]
     * @param  [type] $text       [字体]
     * @param  [type] $color      [颜色]
     * @param  [type] $text_size  [大小]
     * @param  [type] $text_fonts [样式]
     * @param  [type] $text_x     [起始点X]
     * @param  [type] $text_y     [起始点Y]
     * @param  [type] $angle      [倾斜度数]
     * @return [type]             [description]
     */
    public static function text_writer($canvas, $text, $color, $text_size, $text_fonts, $text_x, $text_y, $angle=0)
    {
        // 获取文字信息（基点位置及宽高）
        $text_info = self::text_image($text_size, $text_fonts, $text, $angle);
        // 设置字体颜色（十六进制颜色码转换RGB颜色值与透明度）
        $color = self::color_16($color);
        $color = imagecolorallocatealpha($canvas, $color[0], $color[1], $color[2], $color[3]);
        // 计算文字文字在画布中的位置（以基点为准，文字在图像中的位置）
        $text_x = $text_x ?: (imagesx($canvas) - $text_info['w'])/2;
        if ($text_x < 0) {
            $text_x = imagesx($canvas) - $text_info['w'] + $text_x;
        }
        $text_y = $text_y ?: (imagesy($canvas) - $text_info['h'])/2;

        // 图片添加字体
        imagettftext($canvas, $text_size, $angle, $text_x, $text_y, $color, $text_fonts, $text);
        return $canvas;
    }

    /**
     * [vertical_text_writer 字体垂直写入]
     * @param  [type]  $canvas     [画布]
     * @param  [type]  $text       [文本]
     * @param  [type]  $color      [颜色]
     * @param  [type]  $text_size  [大小]
     * @param  [type]  $text_fonts [样式]
     * @param  [type]  $text_x     [起始点X]
     * @param  [type]  $text_y     [起始点Y]
     * @param  integer $width      [字间距]
     * @return [type]              [description]
     */
    public static function vertical_text_writer($canvas, $text, $color, $text_size, $text_fonts, $text_x, $text_y, $width = 0)
    {
        $textNum = mb_strlen($text);
        // 设置字体颜色（十六进制颜色码转换RGB颜色值与透明度）
        $color = self::color_16($color);
        $color = imagecolorallocatealpha($canvas, $color[0], $color[1], $color[2], $color[3]);

        for ($i=0; $i < $textNum; $i++) {
            $word = mb_substr($text, $i, 1);
            $textInfo = self::text_image($text_size, $text_fonts, $word);
            $width = $width > $textInfo['h'] ? $width : $textInfo['h'];
            $textY = $text_y + $width * $i;

            // 图片添加字体
            imagettftext($canvas, $text_size, 0, $text_x, $textY, $color, $text_fonts, $word);
        }
        return $canvas;
    }

    /**
     * [textalign 计算文本换行，及自动换行]
     * @param  [type] $canvas    [画布]
     * @param  [array] $pos       [top距离画板顶端的距离，fontsize文字的大小，width宽度，left距离左边的距离，hangsize行高]
     * @param  [type] $str       [要写的字符串]
     * @param  [type] $iswrite   [是否输出画入画布  true 画入，false 不画入]
     * @param  [type] $fontpath  [字体文件路径]
     * @param  [type] $nowHeight [已写入行数]
     * @param  [array] $second    [数组 left 记录换行后据x坐标 ,width 记录换行后最大宽; , maxline 记录最大允许最大行数]
     * @return [type]            [description]
     */
    public static function text_align($canvas, $pos, $str, $iswrite, $fontpath, $nowHeight, $second){
        $str = str_replace("\r\n", ' ', str_replace(' ', '', $str)); // 判断换行标签
        $fontsize = $pos["fontsize"];//文字的大小
        $width = $pos["width"];//设置文字换行的宽带，也就是多宽的距离，自动换行
        $textArr = [];
        $temp_string = "";
        $residueStr = '';
        $font_file = $fontpath;//字体文件，在我的同级目录的Fonts文件夹下面
        for ($i = 0; $i < mb_strlen($str,'utf8'); $i++) {
            $box = imagettfbbox($fontsize, 0, $font_file, $temp_string);
            $_string_length = $box[2] - $box[0];
            $temptext = mb_substr($str, $i, 1,'utf-8'); // 拆分字符串
            $temp = imagettfbbox($fontsize, 0, $font_file, $temptext); // 用来测量每个字的大小
            if($second['maxline']){
                //如果已经写入最大行数
                if($nowHeight == $second['maxline']){
                    //获取原字符串长度
                    $strlong = mb_strlen($str,'utf8');
                    //抓取剩余字符串
                    $residueStr .= mb_substr($str, $i, $strlong - $i,'utf-8');
                    $cc = $strlong - $i;
                    break;
                }
            }

            $surplus = ($_string_length + $temp[2] - $temp[0]) - $width;
            if ($temptext == ' ' || $surplus >= 0) { // 换行
                if ($surplus == 0 && $temptext != ' ') {
                    $temp_string .= $temptext;
                } else {
                    $isfuhao = preg_match("/[\\pP]/u", $temptext) ? true : false; // 用来判断最后一个字是不是符合，
                    if ($isfuhao) { // 如果是符号，我们就不换行，把符合添加到最后一个位置去
                        $temp_string = mb_substr($temp_string, 0, mb_strlen($temp_string, 'utf8') - 1, 'utf-8');
                        $i = $i-2;
                    } else {
                        $temptext != ' ' && $i--;
                    }
                }
                $temp = imagettfbbox($fontsize, 0, $font_file, $temp_string); //用来测量每个字的大小
                $textArr[] = [
                    'text' => $temp_string,
                    'surplus' => $temptext == ' ' ? 0 : $width - ($temp[2] - $temp[0])
                ];
                $nowHeight++; // 记录一共写入多少行
                $temp_string = "";
            } else {
                $temp_string .= mb_substr($str, $i, 1,'utf-8');
                if ($i == mb_strlen($str, 'utf8') - 1) {
                    $nowHeight++; // 记录一共写入多少行
                    $textArr[] = ['text' => $temp_string, 'surplus' => 0];
                }
            }
        }

        if ($iswrite && $textArr) {
            $font_color = imagecolorallocate($canvas, $pos["color"][0], $pos["color"][1], $pos["color"][2]);
            foreach ($textArr as $k => $v) {
                $_str_h = $pos['top'] + ($k+1) * $pos['hangsize']; // 计算行高
                if ($v['surplus'] != 0) {
                    $textLen = mb_strlen($v['text'], 'utf8');
                    $spare = $pos['width'] / $textLen;
                    for ($i=0; $i < $textLen; $i++) {
                        $temptext = mb_substr($v['text'], $i, 1,'utf-8');//拆分字符串
                        imagettftext($canvas, $fontsize, 0, $second['left'] + $spare * $i, $_str_h, $font_color, $font_file, $temptext);
                    }
                } else {
                    imagettftext($canvas, $fontsize, 0, $second['left'], $_str_h, $font_color, $font_file, $v['text']);
                }
            }
        }

        $strdata['textArr'] = $textArr;
        $strdata['residueStr'] = $residueStr;
        $strdata['height'] = count($textArr) * $pos['hangsize'];

        return $strdata;
    }

    /**
     * [text_image 获取字体相关属性]
     * @param  [type] $size     [像素单位的字体大小]
     * @param  [type] $fontfile [TrueType 字体文件的文件名]
     * @param  [type] $text     [要度量的字符串]
     * @param  [type] $angle    [text 将被度量的角度大小]
     * @return [type]           [description]
     */
    public static function text_image($size, $fontfile, $text, $angle=0)
    {
        //获取文字信息
        $info = imagettfbbox($size,  $angle, $fontfile, $text);
        $minx = min($info[0], $info[2], $info[4], $info[6]);
        $maxx = max($info[0], $info[2], $info[4], $info[6]);
        $miny = min($info[1], $info[3], $info[5], $info[7]);
        $maxy = max($info[1], $info[3], $info[5], $info[7]);
        // var_dump($minx.'     '.$maxx.'     '.$miny.'     '.$maxy);
        /* 计算文字初始坐标和尺寸 */
        $x = $minx;
        $y = abs($miny);
        $w = $maxx - $minx;
        $h = $maxy - $miny;
        $re = array(
            'x' => $x, // 基点 X 位置
            'y' => $y, // 基点 Y 位置
            'w' => $w, // 字体宽度
            'h' => $h, // 字体高度
        );
        return $re;
    }

    /**
     * [color_16 十六进制颜色码转换RGB颜色值与透明度]
     * @param  string $color [十六进制颜色码]
     * @return [type]        [description]
     */
    public static function color_16($color='#FFFFFF')
    {
        if (is_string($color) && 0 === strpos($color, '#')) {
            $color = str_split(substr($color, 1), 2);
            $color = array_map('hexdec', $color);
            if (empty($color[3]) || $color[3] > 127) {
                $color[3] = 0;
            }
            return $color;
        } else {
            return false;
        }
    }

}
