<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+

/**
 * 解析枚举类型的配置选项
 * @param  [type] $config
 * @return [type]
 */
function parse_config_enum($config)
{
    $array = preg_split('/[\r\n]+/', trim($config, "\r\n"));
    $enum  = [];
    if (strpos($config, ':')) {
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val, 2);
            $enum[$k]    = $v;
        }
    } else {
        $enum = $array;
    }
    return $enum;
}
