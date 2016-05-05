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
class Login extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'account'  => 'require|min:4|max:25',
        'password' => 'require|min:6|max:25',
        'verify'   => 'require|length:4|captcha:cjango',
    ];

    /**
     * 错误提示消息
     * @var array
     */
    protected $message = [
        'account.require'  => '用户名必须填写',
        'account.min'      => '用户名不应少于4个字符',
        'account.max'      => '用户名不应大于25字符',
        'password.require' => '密码必须填写',
        'password.min'     => '密码不应少于6个字符',
        'password.max'     => '密码不应大于25字符',
        'verify.require'   => '验证码必须填写',
        'verify.length'    => '验证码长度不正确',
        'verify.captcha'   => '验证码不正确',
    ];

    protected $scene = [
        'not_verify' => ['account', 'password'],
    ];

    /**
     * 验证码验证规则
     * @param  [type] $value
     * @return boolean
     */
    protected function captcha($value, $rule, $data)
    {
        $ver = new \tools\Captcha();
        return $ver->check($value, 1);
    }
}
