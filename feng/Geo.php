<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2021-04-02T14:04:40+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2021-04-02T14:04:49+08:00
 */

namespace feng;

use think\Config;

/**
 * [Geo redis经纬度相关]
 */
class Geo
{
    // 键名
    protected $key = null;

    // redis实例化
    protected $redis;

    /**
     * LeakyBucket construct
     */
    public function __construct($key = 'lngLat')
    {
        $this->initRedis();
        if (Config::get('database.prefix')) {
            $key = Config::get('database.prefix') . $key;
        }
        if (Config::get('database.database')) {
            $key = Config::get('database.database') . ':' . $key;
        }
        $this->key = $key;
    }

    //初始化redis
    public function initRedis($conf = [])
    {
        if (empty($conf)) {
            if (!empty(Config::get('redis'))) {
                $conf = Config::get('redis');
            }
        }

        $this->redis = $this->init($conf);
    }

    protected function init($redisCfg)
    {
        if (extension_loaded('redis')) {
            if (empty($redisCfg)) {
                return '请配置redis信息！';
            }
            return $this->setupServer($redisCfg);
        } else {
            exit('缺少redis扩展！');
        }
    }

    public function setupServer($config)
    {
        $this->redis = new \Redis();
        if ($config['socket_type'] === 'unix') {
            $success = $this->redis->connect($config['socket']);
        } else {
            $success = $this->redis->connect($config['hostname'], $config['hostport'], $config['timeout']);
        }
        if (!$success) {
            return false;
        } else {
            if (isset($config['password'])) {
                $this->redis->auth($config['password']);
            }

            if (isset($config['db']) && !empty($config['db'])) {
                $this->redis->select(intval($config['db']));
            }
        }

        return $this->redis;
    }

    // geoadd：添加地理位置的坐标。
    // geopos：获取地理位置的坐标。
    // geodist：计算两个位置之间的距离。
    // georadius：根据用户给定的经纬度坐标来获取指定范围内的地理位置集合。
    // georadiusbymember：根据储存在位置集合里面的某个地点获取指定范围内的地理位置集合。
    // geohash：返回一个或多个位置对象的 geohash 值。

    /**
     * [add 添加坐标位置]
     * @param [array] $nameLngLat [坐标点位置array('name'=>'经度,纬度')]
     */
    public function add($nameLngLat)
    {
        $lngLat = '';$i = 0;
        foreach ($nameLngLat as $k => $v) {
            if (strstr($v, ',')) {
                list($lng, $lat) = explode(',', $v);
                $this->redis->rawCommand("GEOADD", $this->key, $lng, $lat, $k);
                $i ++;
            }
        }
        return $i;
    }

    /**
     * [pos description]
     * @param  [type] $name [获取某个点坐标信息]
     * @return [type]       [description]
     */
    public function pos($name)
    {
        $name = self::str_arr($name);
        $lngLat = [];
        foreach ($name as $k => $v) {
            $re = $this->redis->rawCommand("GEOPOS", $this->key, $v);
            if (isset($re[0][0])) {
                $lngLat[$v] = [
                    'lng' => $re[0][0],
                    'lat' => $re[0][1]
                ];
            }
        }
        return $lngLat;
    }

    /**
     * [del 删除某个坐标点，all删除全部]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function del($name)
    {
        if ($name != 'all') {
            $name = self::str_arr($name);
            $i = 0;
            foreach ($name as $k => $v) {
                $re = $this->redis->rawCommand("ZREM", $this->key, $v);
                $i ++;
            }
        } else {
            $i = $this->redis->rawCommand("ZCARD", $this->key);
            $this->redis->del($this->key);
        }

        return $i;
    }

    /**
     * [dist 获取两点之间的距离]
     * @param  [type] $start [起始点]
     * @param  [type] $end   [终点]
     * @param  string $unit  [单位]
     * @return [type]        [description]
     */
    public function dist($start, $end, $unit='m')
    {
        if (is_array($start)) {
            $this->add($start);
            $start = array_keys($start)[0];
        }
        if (is_array($end)) {
            $this->add($end);
            $end = array_keys($end)[0];
        }
        if (!in_array($unit, ['m','km','mi','ft'])) {
            return '该单位不存在';
        }
        $dist = $this->redis->rawCommand("GEODIST", $this->key, $start, $end, $unit);
        return $dist;
    }

    /**
     * [radius 获取范围内的坐标点]
     * @param  [type] $lngLat [圆心名称或经纬度]
     * @param  [type] $radius [距离范围]
     * @param  string $unit   [单位]
     * @param  string $with   [获取数据类型]
     * @param  string $sort   [排序]
     * @return [type]         [description]
     */
    public function radius($lngLat, $radius, $unit="km", $with='WITHDIST', $sort='ASC')
    {
        if (is_array($lngLat)) {
            list($lng, $lat) = array_slice(array_values($lngLat), 0, 2);
            $list = $this->redis->rawCommand("GEORADIUS", $this->key, $lng, $lat, $radius, $unit, $with, $sort);
        } else {
            $list = $this->redis->rawCommand("GEORADIUSBYMEMBER", $this->key, $lngLat, $radius, $unit, $with, $sort);
        }
        return $list;
    }

    public static function str_arr($name)
    {
        strstr($name, ',') && $name = explode(',', $name);
        !is_array($name) && $name = [$name];
        array_filter($name);
        return $name;
    }
}
