<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
// | 扩展函数库                                     |
// +------------------------------------------------+

use think\Cache;
use think\Config;
use think\Db;

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function isLogin()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       数据签名
 */
function data_auth_sign($data = [])
{
    ksort($data);
    $code = http_build_query($data);
    return sha1($code . Config::get('data_auth_key'));
}

/**
 * 用户密码加密
 * @param  string $string 要加密的字符
 * @return string
 */
function umd5($string, $key = '')
{
    $key = $key ? '' : Config::get('data_auth_key');
    return '' === $string ? '' : md5(sha1($string) . $key);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0)
{
    if ($uid == 0) {
        return '';
    }
    static $list;
    if (empty($list)) {
        $list = Cache::get('sys_user_username_list');
    }
    $key = "u{$uid}";
    if (isset($list[$key])) {
        $name = $list[$key];
    } else {
        $info = Db::name('Member')->field('account')->find($uid);
        if ($info && isset($info['account'])) {
            $name  = $list[$key]  = $info['account'];
            $count = count($list);
            $max   = Config::get('user_max_cache');
            while ($count-- > $max) {
                array_shift($list);
            }
            Cache::set('sys_user_username_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0)
{
    if ($uid == 0) {
        return '';
    }
    static $list;
    if (empty($list)) {
        $list = Cache::get('sys_user_nickname_list');
    }
    $key = "u{$uid}";
    if (isset($list[$key])) {
        $name = $list[$key];
    } else {
        $info = Db::name('Member')->field('nickname')->find($uid);
        if ($info !== false && $info['nickname']) {
            $name  = $list[$key]  = $info['nickname'];
            $count = count($list);
            $max   = Config::get('user_max_cache');
            while ($count-- > $max) {
                array_shift($list);
            }
            Cache::set('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return empty($name) ? get_username($uid) : $name;
}

/**
 * 把数据集转换成Tree
 * @param  array   $list  要转换的数据集
 * @param  string  $pk    [description]
 * @param  string  $pid   [description]
 * @param  string  $child [description]
 * @param  integer $root  [description]
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    $tree = [];
    if (is_array($list)) {
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent           = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 获取对应状态的文字信息
 * @param  integer        $status   状态码
 * @param  array|string   $type     string:配置文件中配置键名,array:状态数组
 * @return string         状态文字
 */
function get_status_title($status, $type = null)
{
    if ($status == -1) {
        return '<span style="color:#D2322D;">已删除</span>';
    }
    if (!is_null($type) && is_string($type)) {
        $res = \think\Config::get($type);
        $res = isset($res[$status]) ? $res[$status] : '';
    } elseif (!is_null($type) && is_array($type)) {
        $res = isset($type[$status]) ? $type[$status] : '';
    } else {
        switch ($status) {
            case 0:$res = '<span style="color:#E48600;">禁用</span>';
                break;
            case 1:$res = '<span style="color:#229F24;">正常</span>';
                break;
            case 2:$res = '<span style="color:#39B3D7;">待审核</span>';
                break;
            case 3:$res = '<span style="color:#E48600;">被驳回</span>';
                break;
            default:$res = '';
                break;
        }
    }
    return $res;
}

/**
 * 格式化模板输出
 * @param  string $str 换行符分割的字符串
 * @return string
 */
function pre_echo($str)
{
    $str = preg_replace('/\r\n/', "<br/>", $str);
    return $str;
}

/**
 * 时间戳格式化
 * @param  int    $time
 * @param  string $format
 * @return string 完整的时间显示
 */
function time_format($time = '', $format = '')
{
    $format = !empty($format) ? $format : 'Y-m-d H:i';
    return empty($time) ? '' : date($format, $time);
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function byte_format($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 字符串截取，支持中文和其他编码
 * @param  string $str     需要转换的字符串
 * @param  string $start   开始位置
 * @param  string $length  截取长度
 * @param  string $suffix  截断显示字符
 * @param  string $charset 编码格式
 * @return string
 */
function msubstr($str, $start, $length, $suffix = true, $charset = "utf-8")
{
    if (mb_strlen($str, $charset) < $length) {
        return $str;
    }
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '..' : $slice;
}

/**
 * 检测手机号码格式
 * @param  [type] $mobile [description]
 * @return [type]         [description]
 */
function checkMobile($mobile)
{
    if (preg_match("/^1[3578]{1}[0-9]{9}$|14[57]{1}[0-9]{8}$/", $mobile)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 检测浏览器是否是手机端
 * @return boolean
 */
function isMobile()
{
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    return strpos($userAgent, 'mobile');
}

/**
 * 检测是否是钉钉客户端
 * @return boolean
 */
function isDing()
{
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    return strpos($userAgent, 'dingtalk');
}

/**
 * 检测是否是微信客户端
 * @return boolean
 */
function isWechat()
{
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    return strpos($userAgent, 'wechat');
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param  number  $pos     推荐位的值
 * @param  number  $contain 指定推荐位
 * @return boolean
 */
function check_position($pos = 0, $contain = 0)
{
    if (empty($pos) || empty($contain)) {
        return false;
    }
    return ($pos & $contain) !== 0;
}

/**
 * 显示pos的内容
 * @param  number  $pos  推荐位的值
 * @param  array   $list 推荐位列表
 * @return boolean
 */
function show_position($key, $list)
{
    if (!is_array($key)) {
        return '';
    }
    $str = '';
    foreach ($list as $k => $v) {
        if (check_position($key, $k)) {
            $str .= $v . ';';
        }
    }
    if (!empty($str)) {
        $str = rtrim($str, ';');
    }
    return $str;
}

/**
 * Discuz 经典双向加密/解密
 * @param  string $string     明文 或 密文
 * @param  string $operation  DECODE表示解密,其它表示加密
 * @param  string $key        密匙
 * @param  string $expiry     密文有效期
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length   = 4;
    $key           = md5($key ? $key : C('data_auth_key'));
    $keya          = md5(substr($key, 0, 16));
    $keyb          = md5(substr($key, 16, 16));
    $keyc          = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey      = $keya . md5($keya . $keyc);
    $key_length    = strlen($cryptkey);
    $string        = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result        = '';
    $box           = range(0, 255);
    $rndkey        = [];
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp     = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a       = ($a + 1) % 256;
        $j       = ($j + $box[$a]) % 256;
        $tmp     = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
