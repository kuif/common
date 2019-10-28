<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2019-03-17T14:06:26+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2019-10-28 10:22:51
 */
include './weixinpay.php';

/**
 * 支付示例类
 */
class Demo
{
    private $config = array(
        'APPID'         => '', // 微信支付APPID
        'XCXAPPID'      => '', // 微信小程序APPID
        'MCHID'         => '', // 微信支付MCHID 商户收款账号
        'KEY'           => '', // 微信支付KEY
        'APPSECRET'     => '', // 公众帐号secert
        'NOTIFY_URL'    => '', // 接收支付状态的连接  改成自己的回调地址
    );

    /**
     * [xcxPay 小程序支付]
     * @return [type] [description]
     */
    public function xcxPay()
    {
        $weixinpay = new \feng\Weixinpay($this->config);
        $order_sn = time().rand(1000,9999);

        $order = array(
            'body'          => '测试商品', // 产品描述
            'total_fee'     => '1', // 订单金额（分）
            'out_trade_no'  => $order_sn, // 订单编号
            'product_id'    => $order_sn, // 产品id（可用订单编号）
            'openid'        => $_GET['openid'], // 用户openid
        );
        $re = $weixinpay->xcxPay($order);
        die(json_encode($re)); // JSON化直接返回小程序客户端
    }

    /**
     * [jsPay 公众号支付]
     * @return [type] [description]
     */
    public function jsPay()
    {
        $this->config['REDIRECT_URI'] = 'http://ceshi.test/jsPay'; // 支付页面链接
        $weixinpay = new \feng\Weixinpay($this->config);
        $order_sn = time().rand(1000,9999);

        $order = array(
            'body'          => '测试商品', // 产品描述
            'total_fee'     => '1', // 订单金额（分）
            'out_trade_no'  => $order_sn, // 订单编号
            'product_id'    => $order_sn, // 产品id（可用订单编号）
        );
        $re = $weixinpay->jsPay($order, $_GET['code']);
        die(json_encode($re)); // JSON化直接返回微信端
    } 

    /**
     * [qrcodePay PC扫码支付]
     * @return [type] [description]
     */
    public function qrcodePay()
    {
        $weixinpay = new \feng\Weixinpay($this->config);
        $order_sn = time().rand(1000,9999);

        $order = array(
            'body'          => '测试商品', // 产品描述
            'total_fee'     => '1', // 订单金额（分）
            'out_trade_no'  => $order_sn, // 订单编号
            'product_id'    => $order_sn, // 产品id（可用订单编号）
        );
        $re = $weixinpay->qrcodePay($order);
        qrcode($re); // 返回生成的二维码图片（或返回链接前端生成图片）qrcode为生成二维码函数
    }

    /**
     * [h5Pay 微信网页支付]
     * @return [type] [description]
     */
    public function h5Pay()
    {
        $weixinpay = new \feng\Weixinpay($this->config);
        $order_sn = time().rand(1000,9999);

        $order = array(
            'body'          => '测试商品', // 产品描述
            'total_fee'     => '1', // 订单金额（分）
            'out_trade_no'  => $order_sn, // 订单编号
            'product_id'    => $order_sn, // 产品id（可用订单编号）
        );
        $re = $weixinpay->h5Pay($order);
        return $re; // 返回链接让用户点击跳转
    }

}
