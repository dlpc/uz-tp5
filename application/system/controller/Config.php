<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace app\system\controller;

use think\Cache;
use think\Db;
use think\Loader;
use tools\Consts;

/**
 * 配置管理相关
 */
class Config extends _Init
{
    /**
     * 快速设置系统参数
     */
    public function index()
    {
        if (IS_POST) {
            $config = input('post.config/a', '');
            if ($config && is_array($config)) {
                foreach ($config as $name => $value) {
                    Db::name('Config')->where('name', $name)->setField('value', $value);
                }
            }
            Cache::clear();
            return $this->success('保存成功！');
        } else {
            $map = [
                'hide'   => 0,
                'status' => Consts::STATUS_NORMAL,
            ];
            $list = Db::name('Config')->where($map)->order('sort asc')->select();
            $this->assign('list', $list);

            return $this->fetch();
        }
    }

    /**
     * 参数配置
     * @param  string $title 搜索标题
     * @param  string $group 当前分组
     */
    public function params($title = '', $group = '')
    {
        $map = [
            'hide'   => 0,
            'status' => ['egt', Consts::STATUS_FORBIDDEN],
        ];
        if ($title) {
            $map['title|name'] = ['like', "%{$title}%"];
        }
        if ($group) {
            $map['group'] = $group;
        }
        $list = parent::_list('Config', $map, 'sort asc,create_time desc,update_time desc');
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     * 添加配置
     */
    public function add($group = '')
    {
        if (IS_POST) {
            $data  = input('post.');

            $validate = Loader::validate('Config');
            if (!$validate->check($data)) {
                return error($validate->getError());
            }

            if (Loader::model('Config')::create($data)) {
                Cache::clear();
                return $this->success();
            } else {
                return $this->error();
            }
        } else {
            $info['group'] = $group;
            $this->assign('info', $info);

            return $this->fetch('edit');
        }
    }

    /**
     * 编辑配置
     * @param  integer $id 配置主键
     */
    public function edit($id)
    {
        if (IS_POST) {
            $data  = input('post.');

            $validate = Loader::validate('Config');
            if (!$validate->check($data)) {
                return error($validate->getError());
            }

            if (Loader::model('Config')::update($data)) {
                Cache::clear();
                return $this->success();
            } else {
                return $this->error();
            }
        } else {
            $this->assign('info', Db::name('Config')->find($id));

            return $this->fetch();
        }
    }

    /**
     * 删除配置(假删除)
     * @param  integer $id 配置主键
     */
    public function del($id)
    {
        $map = [
            'id'     => $id,
            'locked' => 0,
        ];
        $data = [
            'status'      => Consts::STATUS_REMOVED,
            'update_time' => NOW_TIME,
        ];
        if (Db::name('Config')->where($map)->update($data)) {
            Cache::clear();
            return $this->success('', 'delete');
        } else {
            return $this->error();
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
            return $this->error();
        }
        $data = [
            'status'      => $status,
            'update_time' => NOW_TIME,
        ];
        if (Db::name('Config')->where('id', $id)->update($data)) {
            Cache::clear();
            return $this->success();
        } else {
            return $this->error();
        }
    }

    /**
     * 快速排序
     * @param  string $group 分组
     */
    public function sort($group = '')
    {
        if (IS_POST) {
            $sort = input('post.sort/a', '');
            if (empty($sort)) {
                return $this->error();
            }
            foreach ($sort as $key => $id) {
                Db::name('Config')->where('id', $id)->setField('sort', $key + 1);
            }
            return $this->success('操作成功', '', Url('system/config/params') . '?group=' . $group);
        } else {
            $map = [
                'hide'   => 0,
                'status' => Consts::STATUS_NORMAL,
            ];
            if ($group) {
                $map['group'] = $group;
            }
            $list = Db::name('Config')->where($map)->order('sort asc')->select();
            $this->assign('list', $list);

            return $this->fetch();
        }
    }

    /**
     * 清除缓存
     */
    public function clear()
    {
        if (Cache::clear()) {
            return $this->success('清除缓存成功');
        } else {
            return $this->error();
        }
    }
}
