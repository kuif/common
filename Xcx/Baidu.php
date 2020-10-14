<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2020-10-13 17:11:17
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2020-10-14T15:39:11+08:00
 */
namespace feng\Xcx;

/**
 * 百度小程序
 */
class Baidu
{
    private static $jscode2session = 'https://spapi.baidu.com/oauth/jscode2sessionkey';
	private static $token = 'https://openapi.baidu.com/oauth/2.0/token';

    private static $config = array(
        'appid' => '', // appid
		'appkey' => '', // appkey
        'secret' => '', // secret
    );

    /**
     * [__construct 构造函数]
     * @param [type] $config [传递支付相关配置]
     */
    public function __construct($config=NULL){
        $config && self::$config = $config;
    }

	/**
	 * [openid 获取 openid]
	 * @param  string $code [code]
	 * @return [type]       [description]
	 */
	public static function openid($code)
	{
		$options = [
			'client_id' => self::$config['appkey'],
			'sk'        => self::$config['secret'],
			'code'      => $code,
		];

		$response = Http::get(self::$jscode2session, $options);
		$result = json_decode($response, true);
		return $result;
	}

	/**
	 * [accessToken 获取 access_token]
	 * @return [type] [description]
	 */
	public static function accessToken()
	{
		$options = [
			'client_id' => self::$config['appkey'],
			'client_secret' => self::$config['secret'],
			'grant_type' => 'client_credentials',
			'scop' => 'smartapp_snsapi_base'
		];

		$response = Http::get(self::$token, $options);
		$result = json_decode($response, true);
		return $result;
	}

}
