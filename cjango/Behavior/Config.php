<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools\Behavior;

/**
 * 配置加载
 */
class Config
{
    /**
     * 行为入口
     * @param  [type] &$params [description]
     * @return [type]          [description]
     */
    public function run(&$content)
    {
        \tools\Config::load();
        \think\Session::init();
    }
}
