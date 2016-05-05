<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
/**
 * [ 应用入口文件 ]
 */

define('ROOT_DOMAIN', '.monler.cn');
/**
 * 系统版本
 */
define('VERSION', '1.0 beta');
/**
 * 定义项目路径
 */
define('APP_PATH', __DIR__ . '/../application/');
/**
 * 开启调试模式
 */
define('APP_DEBUG', true);
/**
 * 修改缓存目录
 */
define('RUNTIME_PATH', __DIR__ . '/../runtime/');
/**
 * 开启钩子模式
 */
define('APP_HOOK', true);
/**
 * 定义当前页面地址
 */
define('__SELF__', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
/**
 * 加载框架引导文件
 */
require __DIR__ . '/../thinkphp/start.php';
