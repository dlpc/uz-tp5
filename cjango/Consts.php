<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

/**
 * 常量定义
 */
class Consts
{
    // 状态码 常量
    const STATUS_DELETED   = -2; // 彻底删除的
    const STATUS_REMOVED   = -1; // 删除的
    const STATUS_FORBIDDEN = 0; // 禁用的
    const STATUS_NORMAL    = 1; // 正常的
    const STATUS_PENDING   = 2; // 待审核
    const STATUS_REJECT    = 3; // 被驳回

    const MEMBER_GENERAL    = 0; // 普通用户
    const MEMBER_COMMERCIAL = 1; // 商户
}
