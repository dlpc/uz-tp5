<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+

use think\Response;
/**
 * [success description]
 * @param  [type] $msg  [description]
 * @param  [type] $url  [description]
 * @param  array  $data [description]
 * @return [type]       [description]
 */
function success($msg = '', $url = '', $data = [])
{
    return Response::success($msg, $data, $url, $wait = 3);
}

/**
 * [error description]
 * @param  [type] $msg  [description]
 * @param  [type] $url  [description]
 * @param  array  $data [description]
 * @return [type]       [description]
 */
function error($msg = '', $url = '', $data = [])
{
    return Response::error($msg, $data, $url, $wait = 3);
}

/**
 * URL重定向
 * @param string $url 跳转的URL表达式
 * @param array|int $params 其它URL参数或http code
 * @return void
 */
function redirect($url, $params = [])
{
    Response::redirect($url, $params);
    exit();
}
