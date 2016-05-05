<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace app\system\controller;

use think\Controller;
use think\Db;
use think\Response;
use tools\Consts;

class _Init extends Controller
{

    /**
     * 后台初始化
     */
    public function _initialize()
    {
        define('UID', isLogin());
        if (!UID) {
            Response::redirect(url('system/login/index'));
            exit;
        }

        // 后台菜单
        if (!IS_AJAX && !IS_POST) {
            $this->assign('_MENU_', self::_getMenus());
        }
    }

    /**
     * 可以分权限展示的菜单返回
     * @return array
     */
    final private function _getMenus()
    {
        $menus = session('system_menu_list');
        if (empty($menus)) {
            // // 找到有权限的菜单的ID
            // $adminUsers = Config::get('administrator');
            // // 当前用户不是超级用户,要做权限验证
            // if (!in_array(UID, $adminUsers)) {
            //     $where['id'] = ['in', \tools\Auth::getMenuIds(UID)];
            // }
            $where['hide']   = 0;
            $where['status'] = 1;
            $menus           = Db::name('menu')->where($where)->order('sort asc,id asc')->field('id,pid,title,icon,url,divider')->select();
            $menus           = list_to_tree($menus);
            session('system_menu_list', $menus);
        }
        $menu = [];
        // 当进入二级菜单的时候,主菜单高亮
        foreach ($menus as $key => $item) {
            $menu['MAIN_MENU'][$key]["id"]      = $item['id'];
            $menu['MAIN_MENU'][$key]["title"]   = $item['title'];
            $menu['MAIN_MENU'][$key]["icon"]    = $item['icon'];
            $menu['MAIN_MENU'][$key]["url"]     = $item['url'];
            $menu['MAIN_MENU'][$key]["divider"] = $item['divider'];
            if (strtolower(CONTROLLER_NAME . '/' . ACTION_NAME) == strtolower($item['url'])) {
                $menu['MAIN_MENU'][$key]['class'] = 'active';
                $mKey                             = $key;
            }
        }
        $map['pid']    = ['neq', 0];
        $map['url']    = CONTROLLER_NAME . '/' . ACTION_NAME;
        $map['status'] = 1;
        $parent_menu   = Db::name('menu')->where($map)->field('id,pid')->find();
        if ($parent_menu) {
            $nav = Db::name('menu')->find($parent_menu['pid']);
            if ($nav['pid']) {
                $nav = Db::name('menu')->find($nav['pid']);
            }
            foreach ($menu['MAIN_MENU'] as $key => $item) {
                if ($item['id'] == $nav['id']) {
                    $menu['MAIN_MENU'][$key]['class'] = 'active';
                    $mKey                             = $key;
                }
            }
            $menu['SUB_TITLE'] = $menus[$mKey]['title'];
            if (isset($menus[$mKey]['_child'])) {
                $menu['SUB_MENU'] = $menus[$mKey]['_child'];
                // 二级菜单高亮
                foreach ($menu['SUB_MENU'] as $key => $item) {
                    unset($menu['SUB_MENU'][$key]['_child']);
                    if ($item['id'] == $parent_menu['id'] || $item['id'] == $parent_menu['pid']) {
                        $menu['SUB_MENU'][$key]['class'] = 'active';
                    }
                }
            }
        }
        return $menu;
    }

    /**
     * 通用分页列表数据集获取方法
     * 如果表单字段有 status 默认会查询 status > 0 的数据
     * @param  sting|Model  $model    模型名或模型实例
     * @param  array        $where    where查询条件(优先级: $where>模型设定)
     * @param  array|string $order    排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                                否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     * @param  boolean      $field    单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @param  integer      $listRows 分页条数
     * @return array|false
     * 返回数据集
     */
    final protected function _list($model, $where = [], $order = '', $field = true, $listRows = 20)
    {
        if (is_string($model)) {
            $table = Db::name($model);
        } else {
            $table = $model;
        }

        // order 默认排序规则
        $pk = $table->getPk();
        if ($order === '' && !empty($pk)) {
            $order = $pk . ' desc';
        }
        // 设置默认查询 > 0 的数据
        if (empty($where)) {
            $fields = $table->getTableInfo('', 'fields');
            if (array_search('status', $fields)) {
                $where['status'] = ['egt', Consts::STATUS_FORBIDDEN];
            }
        }
        $total    = $table->where($where)->count($pk ?: '*');
        $pageShow = '';
        // if ($total > $listRows) {
        //     $page             = new Page($total, $listRows);
        //     $pageShow         = $page->show();
        //     $options['limit'] = $page->firstRow . ',' . $page->listRows;
        // }
        $this->assign('_page', $pageShow);

        if (is_string($model)) {
            $dataModel = Db::name($model);
        } else {
            $dataModel = $model;
        }
        $data = $dataModel->field($field)->where($where)->order($order)->select();
        return $data;
    }

    /**
     * 操作错误跳转的快捷方法
     * @param mixed $msg  提示信息
     * @param mixed $data 返回的数据
     * @param mixed $url  跳转的URL地址
     * @param mixed $wait 跳转等待时间
     * @return void
     */
    final public function error($msg = '', $data = '', $url = '', $wait = 3)
    {
        $msg = $msg ?: '系统繁忙!';
        return Response::error($msg, $data, $url, $wait);
    }

    /**
     * 操作成功跳转的快捷方法
     * @param mixed $msg  提示信息
     * @param mixed $data 返回的数据
     * @param mixed $url  跳转的URL地址
     * @param mixed $wait 跳转等待时间
     * @return void
     */
    final public function success($msg = '', $data = '', $url = '', $wait = 3)
    {
        $msg = $msg ?: '操作成功!';
        return Response::success($msg, $data, $url, $wait);
    }
}
