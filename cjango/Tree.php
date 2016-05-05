<?php
// +------------------------------------------------+
// | http://www.cjango.com                          |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

/**
 * 生成多层树状下拉选框的工具
 */
class Tree
{

    /**
     * 用于树型数组完成递归格式的全局变量
     */
    private static $formatTree;

    /**
     * 生成多层树，供下拉选框使用
     */
    public static function toFormatTree($list, $title = 'title', $pk = 'id', $pid = 'pid', $root = 0)
    {
        $list             = self::list_to_tree($list, $pk, $pid, '_child', $root);
        self::$formatTree = [];
        self::_toFormatTree($list, 0, $title);
        return self::$formatTree;
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
    private static function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
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
     * 将格式数组转换为树
     * @param array   $list
     * @param integer $level 进行递归时传递用的参数
     * @author 小陈叔叔 <Jason.Chen[cjango.com]>
     */
    private static function _toFormatTree($list, $level = 0, $title = 'title')
    {
        foreach ($list as $key => $val) {
            $tmp_str = str_repeat("&nbsp;", $level * 4);
            $tmp_str .= "└&nbsp;";
            $val['level']      = $level;
            $val['title_show'] = $level == 0 ? $val[$title] . "&nbsp;" : $tmp_str . $val[$title];
            if (!array_key_exists('_child', $val)) {
                array_push(self::$formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push(self::$formatTree, $val);
                self::_toFormatTree($tmp_ary, $level + 1, $title); //进行下一层递归
            }
        }
        return;
    }
}
