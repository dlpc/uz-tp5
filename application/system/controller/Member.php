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
use tools\Consts;

/**
 *
 */
class Member extends _Init
{
    /**
     * 个人会员
     */
    public function index($title = '')
    {
        $map = [
            'status' => ['egt', Consts::STATUS_FORBIDDEN],
        ];
        if ($title) {
            $map['account|nickname'] = ['like', "%{$title}%"];
        }

        $list = parent::_list('Member', $map);
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     * [status description]
     * @param  [type] $id     [description]
     * @param  [type] $status [description]
     */
    public function status($id, $status)
    {
        if (!in_array($status, [0, 1])) {
            return $this->error();
        }
        $data = [
            'status'      => $status,
            'update_time' => NOW_TIME,
        ];
        if (Db::name('Member')->where('id', $id)->update($data)) {
            return $this->success();
        } else {
            return $this->error();
        }
    }

}
