<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2024-05-05T17:03:00+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2024-07-01 10:07:22
 */
namespace feng;

use think\Env;
/**
 * 阿里云STS接口
 */
class Alists
{
    protected $url = 'https://sts.aliyuncs.com';
    protected $accessKeyId = ''; // RAM 用户accessKeyId
    protected $accessKeySecret = ''; // RAM accessKeySecret
    protected $roleArn = 'acs:ram::$accountID:role/$roleName';//指定角色的 ARN ，角色策略权限
    protected $roleSessionName = 'fengkui';//用户自定义参数。此参数用来区分不同的 token，可用于用户级别的访问审计。格式：^[a-zA-Z0-9\.@\-_]+$
    protected $durationSeconds = '1800';//指定的过期时间
    
    public function __construct($type = 'sts')
    {
        $this->accessKeyId = Env::get('oss.key_id', false);
        $this->accessKeySecret = Env::get('oss.key_secret', false);
        $this->setRoleArn($type);
    }

    public function sts()
    {
        $action = 'AssumeRole';//通过扮演角色接口获取令牌
        date_default_timezone_set('UTC');
        $param = array(
            'Format'           => 'JSON',
            'Version'          => '2015-04-01',
            'AccessKeyId'      => $this->accessKeyId,
            'SignatureMethod'  => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'SignatureNonce'   => $this->getRandChar(8),
            'Action'           => $action,
            'RoleArn'          => $this->roleArn,
            'RoleSessionName'  => $this->roleSessionName,
            'DurationSeconds'  => $this->durationSeconds,
            'Timestamp'        => date('Y-m-d') . 'T' . date('H:i:s') . 'Z'
            //'Policy'=>'' //此参数可以限制生成的 STS token 的权限，若不指定则返回的 token 拥有指定角色的所有权限。
        );

        $param['Signature'] = $this->computeSignature($param, 'POST');
        $response = http_request($this->url, 'POST', $param);//curl post请求
        $result = json_decode($response, true); 
        if (isset($result['Credentials'])) {
            $utc_time = strtotime($result['Credentials']['Expiration']);
            date_default_timezone_set('PRC');
            return [
                'accessKeySecret' => $result['Credentials']['AccessKeySecret'] ?? '',
                'accessKeyId'     => $result['Credentials']['AccessKeyId'] ?? '',
                'expiration'      => date('Y-m-d H:i:s', $utc_time) ?? '',
                'securityToken'   => $result['Credentials']['SecurityToken'] ?? '',
            ];
        } else {
            return [];
        }
    }

    protected function computeSignature($parameters, $setMethod)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = $setMethod . '&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = $this->getSignature($stringToSign, $this->accessKeySecret . '&');

        return $signature;
    }

    public function getSignature($source, $accessSecret)
    {
        return base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
    }

    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    public function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

    protected function setRoleArn($type)
    {
        if ($type == 'sts') {//根据入参使用不同的策略，当然这里还可以有其他写法兼容更多的策略的情况
            $this->roleArn = '';
        }
    }
}