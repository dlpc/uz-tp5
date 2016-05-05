<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace app\system\controller;

use think\Loader;
use think\Response;

class Database extends _Init
{
    /**
     * [index description]
     * @return [type] [description]
     */
    public function index($tables = null, $id = null, $start = null)
    {
        if (IS_POST && !empty($tables) && is_array($tables)) {
            //初始化
            $config = config('backdata');
            // 检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (!is_writeable($config['path'])) {
                return $this->error('备份目录不存在或不可写，请检查后重试！');
            }
            if (is_file($lock)) {
                return $this->error('检测到一个任务正在执行，请稍后再试！');
            } else {
                file_put_contents($lock, NOW_TIME); //创建锁文件
            }
            session('backup_config', $config);
            //生成备份文件信息
            $file = array(
                'name' => date('Ymd-His', NOW_TIME),
                'part' => 1,
            );
            session('backup_file', $file);
            //缓存要备份的表
            session('backup_tables', $tables);
            //创建备份文件
            $Database = new \tools\Database($file, $config);
            if (false !== $Database->create()) {
                $response = [
                    'code'   => 1,
                    'msg'    => '初始化成功！',
                    'tables' => $tables,
                    'tab'    => ['id' => 0, 'start' => 0],
                ];
                Response::send($response, 'json');
            } else {
                $response = [
                    'code' => 0,
                    'msg'  => '初始化失败，备份文件创建失败！',
                ];
                Response::send($response, 'json');
            }
        } elseif (IS_GET && is_numeric($id) && is_numeric($start)) {
            //备份数据
            $tables = session('backup_tables');
            //备份指定表
            $Database = new \tools\Database(session('backup_file'), session('backup_config'));
            $start    = $Database->backup($tables[$id], $start);
            if (false === $start) {
                //出错
                $response = [
                    'code' => 0,
                    'msg'  => '备份出错！',
                ];
                Response::send($response, 'json');
            } elseif (0 === $start) {
                //下一表
                if (isset($tables[++$id])) {
                    $response = [
                        'code'   => 1,
                        'msg'    => '备份完成！',
                        'tables' => $tables,
                        'tab'    => ['id' => $id, 'start' => 0],
                    ];
                    Response::send($response, 'json');
                } else {
                    //备份完成，清空缓存
                    unlink(session('backup_config.path') . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    $response = [
                        'code' => 1,
                        'msg'  => '备份完成！',
                    ];
                    Response::send($response, 'json');
                }
            } else {
                $rate     = floor(100 * ($start[0] / $start[1]));
                $response = [
                    'code' => 1,
                    'msg'  => "正在备份...({$rate}%)",
                    'tab'  => ['id' => $id, 'start' => $start[0]],
                ];
                Response::send($response, 'json');
            }
        } else {
            $db   = Loader::db();
            $list = $db->query('SHOW TABLE STATUS');
            $list = array_map('array_change_key_case', $list);
            $this->assign('list', $list);
            return $this->fetch();
        }
    }

    /**
     * 优化表
     */
    public function optimize($tables = null)
    {
        if ($tables) {
            $Db = Loader::db();
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list   = $Db->query("OPTIMIZE TABLE `{$tables}`");
                if ($list) {
                    return $this->success("数据表优化完成！");
                } else {
                    return $this->error("数据表优化出错请重试！");
                }
            } else {
                $list = $Db->query("OPTIMIZE TABLE `{$tables}`");
                if ($list) {
                    return $this->success("数据表'{$tables}'优化完成！");
                } else {
                    return $this->error("数据表'{$tables}'优化出错请重试！");
                }
            }
        } else {
            return $this->error("请指定要优化的表！");
        }
    }

    /**
     * 修复表
     */
    public function repair($tables = '')
    {
        if ($tables) {
            $Db = Loader::db();
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list   = $Db->query("REPAIR TABLE `{$tables}`");
                if ($list) {
                    return $this->success("数据表修复完成！");
                } else {
                    return $this->error("数据表修复出错请重试！");
                }
            } else {
                $list = $Db->query("REPAIR TABLE `{$tables}`");
                if ($list) {
                    return $this->success("数据表'{$tables}'修复完成！");
                } else {
                    return $this->error("数据表'{$tables}'修复出错请重试！");
                }
            }
        } else {
            return $this->error("请指定要修复的表！");
        }
    }

    /**
     * 还原数据库
     * @param  integer $time  [description]
     * @param  [type]  $part  [description]
     * @param  [type]  $start [description]
     */
    public function import($time = 0, $part = null, $start = null)
    {
        if (is_numeric($time) && !empty($time) && is_null($part) && is_null($start)) {
            //初始化
            //获取备份文件信息
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = config('backdata.path') . $name;
            $files = glob($path);
            $list  = [];
            foreach ($files as $name) {
                $basename        = basename($name);
                $match           = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz              = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = [$match[6], $name, $gz];
            }
            ksort($list);
            //检测文件正确性
            $last = end($list);
            if (count($list) === $last[0]) {
                session('backup_list', $list); //缓存备份列表
                $response = [
                    'code'  => 1,
                    'msg'   => '初始化完成！',
                    'part'  => 1,
                    'start' => 0,
                ];
                Response::send($response, 'json');
            } else {
                $response = [
                    'code' => 0,
                    'msg'  => '备份文件可能已经损坏，请检查！',
                ];
                Response::send($response, 'json');
            }
        } elseif (is_numeric($part) && is_numeric($start)) {
            $list  = session('backup_list');
            $db    = new \tools\Database($list[$part], ['path' => config('backdata.path'), 'compress' => $list[$part][2]]);
            $start = $db->import($start);
            if (false === $start) {
                $response = [
                    'code' => 0,
                    'msg'  => '还原数据出错！',
                ];
                Response::send($response, 'json');
            } elseif (0 == $start) {
                //下一卷
                if (isset($list[++$part])) {
                    $response = [
                        'code'  => 1,
                        'msg'   => "正在还原...#{$part}",
                        'part'  => $part,
                        'start' => 0,
                    ];
                    Response::send($response, 'json');
                } else {
                    session('backup_list', null);
                    $response = [
                        'code' => 1,
                        'msg'  => '还原完成！',
                    ];
                    Response::send($response, 'json');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0], 'status' => 1);
                if ($start[1]) {
                    $rate     = floor(100 * ($start[0] / $start[1]));
                    $response = [
                        'code'  => 1,
                        'msg'   => "正在还原...#{$part} ({$rate}%)",
                        'part'  => $part,
                        'start' => $start[0],
                    ];
                    Response::send($response, 'json');
                } else {
                    $response = [
                        'code'  => 1,
                        'msg'   => "正在还原...#{$part}",
                        'part'  => $part,
                        'start' => $start[0],
                        'gz'    => 1,
                    ];
                    Response::send($response, 'json');
                }
            }
        } else {
            $path = config('backdata.path');
            $list = [];
            if (is_dir($path)) {
                $flag = \FilesystemIterator::KEY_AS_FILENAME;
                $glob = new \FilesystemIterator($path, $flag);
                $list = [];
                foreach ($glob as $name => $file) {
                    if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
                        $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');
                        $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                        $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                        $part = $name[6];
                        if (isset($list["{$date} {$time}"])) {
                            $info         = $list["{$date} {$time}"];
                            $info['part'] = max($info['part'], $part);
                            $info['size'] = $info['size'] + $file->getSize();
                        } else {
                            $info['part'] = $part;
                            $info['size'] = $file->getSize();
                        }
                        $extension               = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                        $info['compress']        = ($extension === 'SQL') ? '-' : $extension;
                        $info['time']            = strtotime("{$date} {$time}");
                        $list["{$date} {$time}"] = $info;
                    }
                    krsort($list);
                }
            }
            $this->assign('__list__', $list);
            $this->meta_title = '还原数据';
            return $this->fetch();
        }
    }

    /**
     * 删除备份
     */
    public function del($time = 0)
    {
        if ($time) {
            $name = date('Ymd-His', $time) . '-*.sql*';
            $path = config('backdata.path') . $name;
            array_map("unlink", glob($path));
            if (count(glob($path))) {
                return $this->error('备份文件删除失败，请检查权限！');
            } else {
                return $this->success('备份文件删除成功！');
            }
        } else {
            $this->error('参数错误！');
        }
    }
}
