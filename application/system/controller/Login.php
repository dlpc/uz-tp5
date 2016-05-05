<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace app\system\controller;

use think\Db;
use think\Loader;

/**
 * 用户登陆
 */
class Login
{
    /**
     * 登陆
     */
    public function index()
    {
        if (IS_POST) {
            $validate = Loader::validate('Login');
            $data     = input('post.', '');
            if (config('verify_code')) {
                $validateResult = $validate->check($data);
            } else {
                $validateResult = $validate->scene('not_verify')->check($data);
            }
            if (!$validateResult) {
                return error($validate->getError());
            }

            $user = Db::name('member')->where('account', $data['account'])->find();
            if (!$user) {
                return error('用户不存在');
            } elseif ($user['status'] != 1) {
                return error('用户被禁用');
            } elseif ($user['password'] != umd5($data['password'])) {
                return error('密码错误');
            } else {
                $loginData = [
                    'uid'       => $user['id'],
                    'account'   => $user['account'],
                    'login'     => $user['login'],
                    'last_time' => $user['last_time'],
                    'last_ip'   => $user['last_ip'],
                ];
                session('user_auth', $loginData);
                session('user_auth_sign', data_auth_sign($loginData));
                return success('登陆成功', url('system/'));
            }
        } else {
            if (isLogin()) {
                redirect(url('system/'));
            }
            return view();
        }
    }

    /**
     * 验证码
     */
    public function verify()
    {
        $config = [
            'useNoise' => false, // 是否添加杂点
            'imageH'   => 31,
            'fontSize' => 14, // 验证码字体大小(px)
            'length'   => 4, // 验证码位数
            'bg'       => [245, 245, 245], // 背景颜色
            'reset'    => true, // 验证成功后是否重置
        ];

        $ver = new \tools\Captcha($config);
        $ver->entry(1);
    }

    public function logout()
    {
        session('user_auth', null);
        session('user_auth_sign', null);
        session_destroy();
        return success('退出成功', '', url('system/login/index'));
    }
}
