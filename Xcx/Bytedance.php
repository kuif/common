<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2020-10-13 17:11:17
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2020-10-14T15:55:46+08:00
 */
namespace feng\Xcx;

/**
 * 字节跳动小程序
 */
class Bytedance
{
	private static $jscode2session = 'https://developer.toutiao.com/api/apps/jscode2session';
	private static $token = 'https://developer.toutiao.com/api/apps/token';

    private static $config = array(
        'appid' => '', // appid
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
			'code' 	=> $code,
			'appid' => self::$config['appid'],
			'secret' => self::$config['secret'],
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
			'grant_type' => 'client_credential',
			'appid'     => self::$config['appid'],
			'secret'    => self::$config['secret'],
		];

		$response = Http::get(self::$token, $options);
		$result = json_decode($response, true);
		return $result;
	}

}
