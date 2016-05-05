<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace app\common\model;

use think\Model;

class Config extends Model
{

    protected $insert = [
        'status'      => 1,
        'create_time' => NOW_TIME,
        'update_time' => 0,
    ];

    protected $update = [
        'update_time' => NOW_TIME,
    ];

    protected $auto = [
        'range'  => '',
        'hide'   => 0,
        'locked' => 0,
    ];
}
