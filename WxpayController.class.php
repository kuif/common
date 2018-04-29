<?php
namespace Api\Controller;
use Think\Controller;
class WxpayController extends Controller {

    protected function _initialize(){
        
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->api_return(0,'error request method');
        }

    }

    /**
     * [getWXPayData 查询订单，每次调起支付更新单号]
     * @return [type] [description]
     */
    public function getWXPayData()
    {
        $order_id = $_POST['order_id'];
        $user_id = $_POST['user_id'];

        if ($order_id && $user_id) {
            $order = M("Order")->where('pay_status!=1 AND order_id='.$order_id.' AND user_id='.$user_id)->find(); //查询订单是否存在
            $user = M("Users")->where('user_id='.$user_id)->find();
            if ($order && $user && $order['user_id']==$user['user_id']) {

                $body = $order['goods_name'];  //获取到商品描述
                $master_order_sn = date('YmdHis').rand(1000,9999); //生成随机支付单号，随即更新订单中，便于支付回调修改订单状态
                D('order')->where(array("order_id"=>$order_id))->save(array('master_order_sn'=>$master_order_sn));

                $total_fee = $order['order_amount'] * 100; //获取到订单金额已分为单位
                $openid = $user['openid']; //用户唯一标示

                $response = $this->prepay($body, $master_order_sn, $total_fee , $openid); //预支付
                if ($response) {
                    $pay = $this->pay($response['prepay_id']); //获取支付信息去支付
                    if ($pay) {
                        $this->api_return(1,'success',$pay);
                    }else{
                        $this->api_return(-1,'获取支付信息失败');
                    }
                    
                }else{
                    $this->api_return(-1,'预支付失败');
                }
            }else{
                $this->api_return(0,'订单不存在');
            }
        } else {
            $this->api_return(-1,'参数缺失');
        }
    }

    /**
     * 预支付请求接口
     * @param string $openid    openid
     * @param string $body      商品简单描述
     * @param string $order_sn  订单编号
     * @param string $total_fee 金额
     * @return  json的数据
     */
    protected function prepay($body, $out_trade_no, $total_fee,$openid){
        $config = C('WXPAY_XCX_CONFIG');
        
        //统一下单参数构造
        $unifiedorder = array(
            'appid'         => $config['APPID'],
            'mch_id'        => $config['PAY_MCHID'],
            'nonce_str'     => $this->getNonceStr(),
            'body'          => $body,
            'out_trade_no'  => $out_trade_no,
            'total_fee'     => $total_fee,
            'spbill_create_ip'  => get_client_ip(),
            'notify_url'    => 'https://'.$_SERVER['HTTP_HOST'].'/Api/Wxpay/notify.html',
            'trade_type'    => 'JSAPI',
            'openid'        => $openid
        );
        $unifiedorder['sign'] = $this->makeSign($unifiedorder);
        //请求数据
        $xmldata = $this->array2xml($unifiedorder);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res = $this->curl_post_ssl($url, $xmldata);
        if(!$res){
            $this->api_return(0,"Can't connect the server");
        }
        // 这句file_put_contents是用来查看服务器返回的结果 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Statics/log1.txt',$res,FILE_APPEND);
        
        $content = $this->xml2array($res);

        if (strval($content['result_code']) == 'FAIL') {
            return false;
        }else{
            return $content;
        }
    }

    /**
     * 进行支付接口(POST)
     * @param string $prepay_id 预支付ID(调用prepay()方法之后的返回数据中获取)
     * @return  json的数据
     */
    protected function pay($prepay_id){
        $config = C('WXPAY_XCX_CONFIG');
        
        $data = array(
            'appId'     => $config['APPID'],
            'timeStamp' => time(),
            'nonceStr'  => $this->getNonceStr(),
            'package'   => 'prepay_id='.$prepay_id,
            'signType'  => 'MD5'
        );
        
        $data['paySign'] = $this->makeSign($data);
        
        // $this->ajaxReturn($data);
        return $data;
    }

    //微信支付回调验证
    public function notify(){
        $xml = file_get_contents('php://input');
        
        // 这句file_put_contents是用来查看服务器返回的XML数据 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Statics/log2.txt',$xml,FILE_APPEND);
        
        //将服务器返回的XML数据转化为数组
        $data = $this->xml2array($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        unset($data['sign']);
        $sign = $this->makeSign($data);
        
        // 判断签名是否正确  判断支付状态
        if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') && ($data['result_code']=='SUCCESS') ) {
            $result = $data;
            //获取服务器返回的数据
            $order_sn = $data['out_trade_no'];          //订单单号
            $openid = $data['openid'];                  //付款人openID
            $total_fee = $data['total_fee'];            //付款金额
            $transaction_id = $data['transaction_id'];  //微信支付流水号
            
            //更新数据库订单状态
            $aaaa = $this->updateDB($order_sn,$openid,$total_fee,$transaction_id);
            
        }else{
            $result = false;
        }
        // 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;
    }

    //---------------------------------------------------------------用到的函数------------------------------------------------------------

    /**
     * [api_return 接口返回数据提示]
     * @param  [type] $status   [状态信息]
     * @param  [type] $messages [提示信息]
     * @param  [type] $data     [返回数据]
     * @param  string $next_url [下一个URL]
     * @param  string $pre_url  [上一个URL]
     * @return [type]           [JSON返回提示]
     */
    protected function api_return($status,$messages,$data,$next_url="",$pre_url=""){
        $request = array(
            'status'    => $status,     //状态
            'messages'  => $messages,   //信息
            'data'      => $data,       //数据
            'next_url'  => $next_url,   //下个url
            'pre_url'   => $pre_url,   //上一个url
            'time'      => time(),      //数据返回时间
        );
        exit(json_encode($request));
    }

    /**
     * [array2xml 将一个数组转换为 XML 结构的字符串]
     * @param  [type]  $arr   [要转换的数组]
     * @param  integer $level [节点层级, 1 为 Root.]
     * @return [type]         [结构的字符串]
     */
    protected function array2xml($arr, $level = 1) {
        $s = $level == 1 ? "<xml>" : '';
        foreach($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if(!is_array($value)) {
                $s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1)."</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s."</xml>" : $s;
    }

    /**
     * [xml2array 将xml转为array]
     * @param  [type] $xml [$xml xml字符串]
     * @return [type]      [转换得到的数组]
     */
    protected function xml2array($xml){   
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $result;
    }

    /**
     * [getNonceStr 产生随机字符串，不长于32位]
     * @param  integer $length [长度]
     * @return [type]          [产生的随机字符串]
     */
    protected function getNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        } 
        return $str;
    }

    /**
     * [makeSign 生成签名]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function makeSign($data){
        $config = C('WXPAY_XCX_CONFIG');
        //获取微信支付秘钥
        $key = $config['PAY_APIKEY'];
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }

    /**
     * [curl_post_ssl 微信支付发起请求]
     * @param  [type]  $url     [description]
     * @param  [type]  $xmldata [description]
     * @param  integer $second  [description]
     * @param  array   $aHeader [description]
     * @return [type]           [description]
     */
    protected function curl_post_ssl($url, $xmldata, $second=30,$aHeader=array()){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        
     
        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
     
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xmldata);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else { 
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n"; 
            curl_close($ch);
            return false;
        }
    }

}