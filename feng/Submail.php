<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2021-12-16T15:23:56+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2021-12-16T15:23:56+08:00
 */
namespace feng;

/**
 * 赛邮短信与邮件发送
 */
class Submail
{
    // 发送短信
    private static $messageSendUrl = 'https://api.mysubmail.com/message/send.json';
    private static $messageXsendUrl = 'https://api.mysubmail.com/message/xsend.json';

    // 发送邮件
    private static $mailSendUrl = 'https://api.mysubmail.com/mail/send.json';

    private static $config = array(
        'message_appid'     => '',
        'message_appkey'    => '',
        'mail_appid'        => '',
        'mail_appkey'       => '',
        'sign_type'         => 'normal'
    );

    /**
     * [__construct 构造函数]
     * @param [type] $config [传递相关配置]
     */
    public function __construct($config=NULL){
        $config && self::$config = array_merge(self::$config, $config);
    }

    /**
     * [message 发送短信]
     * @param  [type] $to      [接受者]
     * @param  [type] $content [具体内容，（模板消息是传数组）]
     * @param  string $temp    [模板ID，可不传]
     * @return [type]          [description]
     */
    public static function message($to, $content, $temp = '')
    {
        $sendUrl = $temp ? self::$messageXsendUrl : self::$messageSendUrl;

        $postData = array(
            'appid'     => self::$config['message_appid'],
            'to'        => $to,
            'timestamp' => time(),
            'sign_type' => self::$config['sign_type'],
        );
        if ($temp) {
            $postData['project'] = $temp;
            $postData['vars'] = json_encode($content);
        } else {
            $postData['content'] = is_array($content) ? json_encode($content) : $content;
        }

        $postData['signature'] = self::makeSignature($postData, 'message');
        $response = http_request($sendUrl, 'POST', json_encode($postData), ['Content-Type: application/json']);
        $result = json_decode($response, true);

        return $result;
    }

    /**
     * [mail 发送邮件]
     * @param  [type] $to      [收件人]
     * @param  [type] $from    [发件人]
     * @param  string $subject [标题]
     * @param  array  $data    [其他邮件具体内容]
     * @return [type]          [description]
     */
    public static function mail($to, $from, $subject='', $data = [])
    {
        $sendUrl = self::$mailSendUrl;
        $to = is_array($to) ? $to : (strstr($to,',') ? explode(',',$to) : $to);
        $to = is_array($to) ? array_unique(array_filter($to)) : [$to];
        foreach ($to as $k => $v) {
            $to[$k] = '<' . $v . '>';
        }
        $to = implode(',', $to);
        $postData = array(
            'appid'     => self::$config['mail_appid'],
            'to'        => $to,
            'from'      => $from,
            'subject'   => $subject,
            'timestamp' => time(),
            'sign_type' => self::$config['sign_type'],
            'asynchronous' => true,
        );

        $postData = array_merge($postData, $data);
        $postData['signature'] = self::makeSignature($postData, 'mail');

        $response = http_request($sendUrl, 'POST', json_encode($postData), ['Content-Type: application/json']);
        $result = json_decode($response, true);
        return $result;
    }

    /**
     * [makeSignature 生成签名]
     * @param  [type] $request [请求参数]
     * @param  string $type    [类型]
     * @return [type]          [description]
     */
    protected static function makeSignature($request, $type = 'message'){
        $signType = self::$config['sign_type'];
        $app = self::$config[$type . '_appid'];
        $appkey = self::$config[$type . '_appkey'];
        if ($signType == 'normal')
            return $appkey;

        ksort($request);
        $arg = urldecode(http_build_query($request));

        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
        if($signType == 'sha1'){
            $signature = sha1($app.$appkey.$arg.$app.$appkey);
        }else{
            $signature = md5($app.$appkey.$arg.$app.$appkey);
        }
        return $signature;
    }

}
