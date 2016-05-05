<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

/**
 * 加载系统配置,依赖数据库和缓存
 */
class Config
{
    /**
     * 加载系统扩展配置
     * @param  string $range 配置作用域
     */
    public static function load($range = '')
    {
        $config = \think\Cache::get('db_config_data_' . $range);
        if (!$config) {
            if ($range) {
                $map['range'] = $range;
            }
            $map['status'] = 1;
            // 在这里先判断一下数据库是否已经正确安装
            $Db    = \think\Loader::db();
            $Query = $Db->query("SHOW TABLES LIKE '" . \think\Config::get('database.prefix') . "config'");
            if (empty($Query)) {
                self::install();
            }
            $data   = \think\Db::name('config')->where($map)->field('type,name,value')->select();
            $config = [];
            if ($data && is_array($data)) {
                foreach ($data as $value) {
                    $config[$value['name']] = self::parse($value['type'], $value['value']);
                }
            }
            \think\Cache::set('db_config_data_' . $range, $config);
        }
        \think\Config::set($config);
    }

    /**
     * 初始化安装
     */
    private static function install()
    {
        $database = new Database([1 => __DIR__ . '/install.sql'], ['compress' => false]);
        $database->import();
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     * @return array
     */
    private static function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[\r\n]+/', trim($value, "\r\n"));
                if (strpos($value, ':')) {
                    $value = [];
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val, 2);
                        $value[$k]   = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }

    /**
     * 清除配置缓存
     */
    public static function clear()
    {
        S('db_config_data_', null);
    }
}
