<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2019-03-17T14:06:26+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2019-05-22T14:08:02+08:00
 */

$order = array(
    'body'          => '', // 产品描述
    'total_fee'     => '', // 订单金额（分）
    'out_trade_no'  => '', // 订单编号
    'product_id'    => '', // 产品id（可用订单编号）
    'trade_type'    => '', // 类型：JSAPI--JSAPI支付（或小程序支付）、NATIVE--Native支付、APP--app支付，MWEB--H5支付
);

$config = array(
     'APPID'        => '', // 微信支付APPID
     'XCXAPPID'     => '', // 微信小程序APPID
     'MCHID'        => '', // 微信支付MCHID 商户收款账号
     'KEY'          => '', // 微信支付KEY
     'APPSECRET'    => '', // 公众帐号secert
     'NOTIFY_URL'   => '', // 接收支付状态的连接  改成自己的回调地址
);

/**
 * [weixinQr 微信扫码支付]
 * @param  [type] $pay_order [description]
 * @return [type]            [description]
 */
public function weixinQr($pay_order)
{
    $pay_order = array(
        'body'          => '', // 产品描述
        'total_fee'     => '', // 订单金额（分）
        'out_trade_no'  => '', // 订单编号
        'product_id'    => '', // 产品id（可用订单编号）
        'trade_type'    => 'NATIVE', // Native支付
    );
    $weixinpay = new \feng\Weixinpay($config); // 传入支付配置文件
    $result = $weixinpay->unifiedOrder($pay_order);
    $decodeurl = urldecode($result['code_url']);
    qrcode($decodeurl); // 使用二维码生成函数生成二维码图片
}

/**
 * [weixinH5 微信H5支付]
 * @param  [type] $pay_order [订单信息数组]
 * @return [type]            [description]
 */
public function weixinH5($pay_order)
{
    $pay_order = array(
        'body'          => '', // 产品描述
        'total_fee'     => '', // 订单金额（分）
        'out_trade_no'  => '', // 订单编号
        'product_id'    => '', // 产品id（可用订单编号）
        'trade_type'    => 'MWEB', // H5支付
    );
    $weixinpay = new \feng\Weixinpay($config); // 传入支付配置文件
    $result = $weixinpay->unifiedOrder($pay_order);
    if ($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS')
        return $result['mweb_url']; // 返回链接页面直接跳转
    return false;
}


/**
 * [weixinXcx 微信小程序支付]
 * @param  [type] $pay_order [订单信息数组]
 * @return [type]            [description]
 */
public function weixinXcx($pay_order)
{
    $pay_order = array(
        'body'          => '', // 产品描述
        'total_fee'     => '', // 订单金额（分）
        'out_trade_no'  => '', // 订单编号
        'product_id'    => '', // 产品id（可用订单编号）
        'openid'        => '', // 用户openid
        'trade_type'    => 'JSAPI', // 小程序支付
    );
    $weixinpay = new \feng\Weixinpay($config); // 传入支付配置文件
    $result = $weixinpay->unifiedOrder($pay_order);
    if ($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS') {
        $pay_return['wdata'] = array (
            'appId'		=> $config['XCXAPPID'],
            'timeStamp'	=> time(),
            'nonceStr'	=> get_rand_str('32'), // 随机32位字符串
            'package'	=> 'prepay_id='.$result['prepay_id'],
            'signType'	=> 'MD5', // 加密方式
        );
        $pay_return['wdata']['paySign'] = $weixinpay->makeSign($pay_return['wdata']);
        $pay_return['pay_money'] = $pay_order['total_fee'];
        return $pay_return; // 数据小程序客户端
    } else {
        return false;
    }
}
