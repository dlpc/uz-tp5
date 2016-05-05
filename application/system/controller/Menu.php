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
use tools\Consts;

/**
 * 系统菜单管理
 */
class Menu extends _Init
{
    /**
     * 菜单列表
     * @param  integer $pid [description]
     */
    public function index($pid = 0, $title = '')
    {
        $map = [
            'status' => ['egt', Consts::STATUS_FORBIDDEN],
        ];
        if ($title) {
            $map['title'] = array('like', "%{$title}%");
        } else {
            $map['pid'] = $pid;
        }

        $list = parent::_list('Menu', $map, 'sort asc');
        $this->assign('list', $list);

        // 地图
        $nodesMap = [
            'status' => ['egt', Consts::STATUS_FORBIDDEN],
        ];
        $nodes = Db::name('Menu')->field('id,pid,title,"true" as open,concat("?pid=",id) as url,"_self" as target')
            ->where($nodesMap)->order('sort asc')->select();
        $nodes = json_encode($nodes);
        $this->assign('nodes', $nodes);

        return $this->fetch();
    }

    /**
     * 新增
     */
    public function add($pid = '')
    {
        if (IS_POST) {
            $data  = input('post.');

            $validate = Loader::validate('Menu');
            if (!$validate->check($data)) {
                return error($validate->getError());
            }

            if (model('Menu')::create($data)) {
                session('system_menu_list', null);
                return $this->success();
            } else {
                return $this->error();
            }
        } else {
            $this->assign('up_menus', self::_treeShow());
            $this->assign('info', ['pid' => $pid]);

            return $this->fetch('edit');
        }
    }

    /**
     * 编辑
     * @param  [type] $id 主键
     */
    public function edit($id)
    {
        if (IS_POST) {
            $data  = input('post.');

            $validate = Loader::validate('Menu');
            if (!$validate->check($data)) {
                return error($validate->getError());
            }

            if (model('Menu')::update($data)) {
                session('system_menu_list', null);
                return $this->success();
            } else {
                return $this->error();
            }
        } else {
            $this->assign('up_menus', self::_treeShow($id));
            $this->assign('info', Db::name('Menu')->where('id', $id)->find());

            return $this->fetch();
        }
    }

    private function _treeShow($id = 0)
    {
        $map = [];
        if ($id) {
            $map['id'] = ['neq', $id];
        }

        $menus = Db::name('Menu')->where($map)->order('sort asc')->select();
        $menus = \tools\Tree::toFormatTree($menus);
        $menus = array_merge([0 => ['id' => 0, 'title_show' => '顶级菜单']], $menus);
        return $menus;
    }

    /**
     * 删除菜单
     * @param  [type] $id
     */
    public function del($id)
    {
        if (Db::name('Menu')->where('pid', $id)->find()) {
            return $this->error('该菜单有下级菜单,不允许直接删除');
        }
        if (Db::name('Menu')->where('id', $id)->delete()) {
            session('system_menu_list', null);
            return $this->success('', 'delete');
        } else {
            return $this->error();
        }
    }

    /**
     * 快速排序
     * @param  [type] $pid
     */
    public function sort($pid)
    {
        if (IS_POST) {
            $sort = input('post.sort/a', '');
            if (empty($sort)) {
                return $this->error();
            }
            foreach ($sort as $key => $id) {
                Db::name('Menu')->where('id', $id)->setField('sort', $key + 1);
            }
            session('system_menu_list', null);
            return $this->success('操作成功', '', Url('system/menu/index') . '?pid=' . $pid);
        } else {
            $map = [
                'pid'    => $pid,
                'status' => 1,
            ];
            $list = Db::name('Menu')->where($map)->order('sort asc')->select();
            $this->assign('list', $list);

            return $this->fetch();
        }
    }

    /**
     * 快速修改状态
     * @param  [type] $id
     * @param  [type] $status
     */
    public function status($id, $status)
    {
        if (!in_array($status, [0, 1])) {
            $this->error();
        }

        $data = [
            'status'      => $status,
            'update_time' => NOW_TIME,
        ];
        if (Db::name('Menu')->where('id', $id)->update($data)) {
            session('system_menu_list', null);
            return $this->success();
        } else {
            return $this->error();
        }
    }

    /**
     * 快速修修改显隐状态
     * @param  [type] $id
     * @param  [type] $hide
     */
    public function hide($id, $hide)
    {
        if (!in_array($hide, [0, 1])) {
            $this->error();
        }

        $data = [
            'hide'        => $hide,
            'update_time' => NOW_TIME,
        ];
        if (Db::name('Menu')->where('id', $id)->update($data)) {
            session('system_menu_list', null);
            return $this->success();
        } else {
            return $this->error();
        }
    }

    /**
     * 快速修修改显隐状态
     * @param  [type] $id
     * @param  [type] $hide
     */
    public function auth($id, $auth)
    {
        if (!in_array($auth, [0, 1])) {
            $this->error();
        }

        $data = [
            'auth'        => $auth,
            'update_time' => NOW_TIME,
        ];
        if (Db::name('Menu')->where('id', $id)->update($data)) {
            session('system_menu_list', null);
            return $this->success();
        } else {
            return $this->error();
        }
    }
}
