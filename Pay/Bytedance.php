<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2020-05-13 17:02:49
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2020-10-14T16:48:25+08:00
 */
namespace feng\Pay;

use Yansongda\Pay\Pay;

class Bytedance
{
    private static $config = array(
        'mch_id'    => '', // 商户号
        'app_id'    => '', // App ID
        'secret'    => '', // 支付secret
        // 'notify_url' => '', // 支付回调地址
    );

    /**
     * [__construct 构造函数]
     * @param [type] $config [传递支付相关配置]
     */
    public function __construct($config=NULL){
        $config && self::$config = $config;
    }

    /**
     * [xcxPay 字节跳动小程序支付]
     * @param  string $order [订单信息]
     * @param  string $type  [支付类型 wechat ali]
     * @param  string $url   [对应支付地址]
     * @return [type]        [description]
     * $order = array(
     *      'body'          => '', // 产品描述
     *      'total_amount'  => '', // 订单金额（分）
     *      'order_sn'      => '', // 订单编号
     * );
     */
    public static function xcxPay($order='', $alipay=[], $wechat=[])
    {
        if (!is_array($order) || count($order) < 3 || (!$alipay && !$wechat))
            die("数组数据信息缺失！");

        $config = self::$config;
        $time = time();
        $data = [
            "app_id"        => $config['app_id'],
            "sign_type"     => "MD5",
            "out_order_no"  => (string)$order['order_sn'],
            "merchant_id"   => $config['mch_id'],
            "timestamp"     => (string)$time,
            "product_code"  => "pay",
            "payment_type"  => "direct",
            "total_amount"  => $order['total_amount'],
            "trade_type"    => "H5",
            "uid"           => self::getNonceStr(),
            "version"       => "2.0",
            "currency"      => "CNY",
            "subject"       => $order['body'],
            "body"          => $order['body'],
            "trade_time"    => (string)$time,
            "valid_time"    => "300",
            "notify_url"    => $config['notify_url'],
            // "risk_info" => json_encode(['ip' => self::get_iP()])
        ];

        if ($alipay) { // 支付宝支付
            $aliOrder = array(
                'out_trade_no'  => $order['order_sn'],
                'total_amount'  => $order['total_amount']/100, // **单位：元**
                'subject'       => $order['body'],
            );
            $alipayUrl = Pay::alipay($alipay)->app($aliOrder);
            $data['alipay_url'] = $alipayUrl->getContent();
        }
        if ($wechat) { // 微信支付
            $wechatOrder = [
                'out_trade_no'  => $order['order_sn'],
                'total_fee'     => $order['total_amount'], // **单位：分**
                'body'          => $order['body'],
            ];
            $wxUrl = Pay::wechat($wechat)->wap($wechatOrder);
            $data['wx_url'] = $wxUrl->getTargetUrl();
            $data['wx_type'] = 'MWEB';
        }
        $data["sign"] = self::makeSign($data, $config['secret']);
        $data["risk_info"] = json_encode(['ip' => self::get_iP()]);
        return $data;
    }

    /**
     * [makeSign 生成签名]
     * @param  [type] $data   [入参数据]
     * @param  string $secret [微信支付秘钥]
     * @return [type]         [description]
     */
    protected static function makeSign($data, $secret = ''){
        // 获取微信支付秘钥
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);

        //签名步骤二：在string后加入KEY
        //$config=self::$config;
        $string_sign_temp = $string_a . $secret;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        // $result=strtoupper($sign);
        return $sign;
    }

    /**
     * [getNonceStr 产生随机字符串，不长于32位]
     * @param  integer $length [长度]
     * @return [type]          [description]
     */
    protected static function getNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * [antidirt 检测文本是否包含违规内容]
     * @param  string $text [description]
     * @return [type]       [description]
     */
    public static function antidirt($text='')
    {
        if (!$text)
            die('请输入要筛选的词');

        $appid = 'ttd32da5904c07a571';
        $secret = 'd22ca6e01cc7f95cb8b015581e58f111337818bb';
        $antidirtUrl = 'https://developer.toutiao.com/api/v2/tags/text/antidirt';
        $tokenUrl = 'https://developer.toutiao.com/api/apps/token?appid='.$appid.'&secret'.$secret.'=&grant_type=client_credential';

        $data = httpRequest($tokenUrl, 'GET');
        if (!$data)
            die('access_token获取失败');

        $data = json_decode($data,TRUE);
        $access_token = $data['access_token'];
        $contentbody = '{"tasks": [{"content": "'.$text.'"}]}';
        $re = httpRequest($antidirtUrl, 'POST',$contentbody, ['X-Token: '.$access_token]);
        $re = json_decode($re, TRUE);

        if ($re['data'][0]['predicts'][0]['hit']) {
            die('包含违规内容，请更换');
            return true;
        } else {
            return true;
        }
    }

    /** fengkui.net
     * [get_iP 定义一个函数get_iP() 客户端IP]
     * @return [type] [description]
     */
    public static function get_iP()
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

}
