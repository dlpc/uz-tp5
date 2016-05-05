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
class Config extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'title' => 'require|min:3|max:32',
        'name'  => 'require|min:3|max:32|unique:config',
        'sort'  => 'number',
        'value' => 'require',
    ];

    /**
     * 错误提示消息
     * @var array
     */
    protected $message = [
        'title.require' => '配置名称必须填写',
        'title.min'     => '配置名称不应少于3个字符',
        'title.max'     => '配置名称不应大于32字符',
        'name.require'  => '配置标识必须填写',
        'name.min'      => '配置标识不应少于3个字符',
        'name.max'      => '配置标识不应大于32字符',
        'name.unique'   => '配置标识已经存在',
        'sort.number'   => '排序必须是数字',
        'value.require' => '配置值必须填写',
    ];
}
