<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace app\common\validate;

use think\Validate;

/**
 * 用户登录验证器
 */
class Menu extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'title' => 'require|max:32',
        'sort'  => 'number',
        'url'   => 'require',
    ];

    /**
     * 错误提示消息
     * @var array
     */
    protected $message = [
        'title.require' => '菜单名称必须填写',
        'title.max'     => '菜单名称不应大于32字符',
        'sort.number'   => '排序必须是数字',
        'url.require'   => '菜单URL连接必须填写',
    ];
}
