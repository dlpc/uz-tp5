<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
return [
    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------
    'log'              => [
        'type'        => 'trace', // 支持 socket trace file
        // 'path'        => LOG_PATH . MODULE_NAME . DS . date('Y/m') . DS,
        'path'        => LOG_PATH . MODULE_NAME . DS,
        'time_format' => ' c ',
        'file_size'   => 2097152,
    ],
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    'view_replace_str' => [
        '__SELF__' => $_SERVER['REQUEST_URI'],
        '__CDN__'  => '//uzcdn' . ROOT_DOMAIN,
        '__CSS__'  => '//uzcdn' . ROOT_DOMAIN . '/' . MODULE_NAME . '/css',
        '__JS__'   => '//uzcdn' . ROOT_DOMAIN . '/' . MODULE_NAME . '/js',
        '__IMG__'  => '//uzcdn' . ROOT_DOMAIN . '/' . MODULE_NAME . '/img',
        '__LIB__'  => '//uzcdn' . ROOT_DOMAIN . '/' . MODULE_NAME . '/lib',
    ],
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    'session'          => [
        'var_session_id' => 'session_id', // SESSION_ID的提交变量,解决flash上传跨域
        'prefix'         => 'cjango_system',
        'auto_start'     => true,
        'path'           => '/tmp',
    ],
    // +----------------------------------------------------------------------
    // | 数据备份设置
    // +----------------------------------------------------------------------
    'backdata'         => [
        'path'     => realpath('../backup/data/') . DS, // 数据库备份根路径
        'part'     => 2097152, // 数据库备份卷大小 2Mb
        'compress' => true, // 数据库备份文件是否启用压缩
        'level'    => 9, // 数据库备份文件压缩级别
    ],
];
