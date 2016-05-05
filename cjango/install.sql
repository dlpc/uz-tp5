-- -----------------------------
-- c.Jango MySQL Data Transfer
--
-- Host     : 127.0.0.1
-- UserName : uz_monler
-- Database : uz_monler
--
-- Part : #1
-- Date : 2016-05-05 12:53:32
-- -----------------------------


-- -----------------------------
-- Table structure for `go_config`
-- -----------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `go_config`;
CREATE TABLE `go_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '配置标识',
  `type` tinyint(2) unsigned NOT NULL COMMENT '类型',
  `range` varchar(20) NOT NULL COMMENT '作用域',
  `title` varchar(40) NOT NULL COMMENT '名称',
  `group` tinyint(2) unsigned NOT NULL COMMENT '分组',
  `extra` text NOT NULL COMMENT '扩展',
  `remark` varchar(255) NOT NULL COMMENT '备注说明',
  `value` text NOT NULL COMMENT '默认值',
  `sort` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `hide` tinyint(1) unsigned NOT NULL COMMENT '是否隐藏',
  `locked` tinyint(1) unsigned NOT NULL COMMENT '是否锁定,不允许删除',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4;

-- -----------------------------
-- Records of `go_config`
-- -----------------------------
INSERT INTO `go_config` VALUES ('1', 'config_type_list', '3', '', '配置类型', '2', '', '', '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', '2', '1', '1', '1000000000', '0', '1');
INSERT INTO `go_config` VALUES ('2', 'config_group_list', '3', '', '配置分组', '2', '', '', '1:基本\r\n2:系统\r\n3:字典', '6', '0', '1', '1000000000', '0', '1');
INSERT INTO `go_config` VALUES ('3', 'web_site_title', '1', '', '系统名称', '1', '', '', '友卓科技', '3', '0', '0', '0', '1462423961', '1');
INSERT INTO `go_config` VALUES ('4', 'boolean_status', '3', '', '布尔值参数', '3', '', '', '0:<span style="color:#D2322D;">否</span>\r\n1:<span style="color:#229F24;">是</span>', '4', '0', '0', '0', '0', '1');
INSERT INTO `go_config` VALUES ('5', 'module_list', '3', '', '模块列表', '3', '', '', 'system:后台\r\nuser:用户中心', '5', '0', '0', '0', '0', '1');
INSERT INTO `go_config` VALUES ('6', 'verify_code', '4', '', '是否开启验证码', '1', '0:关闭\r\n1:开启', '', '0', '1', '0', '0', '1461897151', '1462423740', '1');
INSERT INTO `go_config` VALUES ('100', 'sex', '3', '', '性别', '3', '', '', '0:保密\r\n1:男\r\n2:女', '7', '0', '0', '1455862450', '1462423994', '1');

-- -----------------------------
-- Table structure for `go_member`
-- -----------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `go_member`;
CREATE TABLE `go_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(40) NOT NULL,
  `password` varchar(32) NOT NULL,
  `login` int(10) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  `last_time` varchar(20) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL,
  `birthday` date NOT NULL,
  `headimgurl` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- -----------------------------
-- Records of `go_member`
-- -----------------------------
INSERT INTO `go_member` VALUES ('1', 'root', 'ca754f34a2821c1bc2cc36fb086466a4', '24', '1455603094', '1462423846', '1', '222.32.4.178', '1455777110', '小陈叔叔', '0', '2016-02-24', '');

-- -----------------------------
-- Table structure for `go_menu`
-- -----------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `go_menu`;
CREATE TABLE `go_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `icon` varchar(20) NOT NULL,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(30) NOT NULL DEFAULT '' COMMENT '链接地址',
  `divider` tinyint(1) unsigned NOT NULL,
  `auth` tinyint(1) unsigned NOT NULL COMMENT '需要权限验证',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `go_menu`
-- -----------------------------
INSERT INTO `go_menu` VALUES ('1', '系统设置', 'cog', '0', '2', 'config/index', '1', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('2', '系统配置', 'cogs', '1', '1', 'config/index', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('3', '配置参数', 'bars', '1', '2', 'config/params', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('4', '菜单管理', 'list', '1', '3', 'menu/index', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('5', '备份数据', 'server', '1', '6', 'database/index', '1', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('6', '数据还原', 'circle-o-notch', '1', '7', 'database/import', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('7', '增加配置', '', '3', '1', 'config/add', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('8', '编辑配置', '', '3', '2', 'config/edit', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('9', '删除配置', '', '3', '3', 'config/del', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('10', '快速排序', '', '3', '4', 'config/sort', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('11', '状态', '', '3', '5', 'menu/status', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('12', '清除缓存', '', '3', '5', 'config/clear', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('13', '添加菜单', '', '4', '1', 'menu/add', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('14', '编辑菜单', '', '4', '2', 'menu/edit', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('15', '删除菜单', '', '4', '3', 'menu/del', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('16', '快速排序', '', '4', '4', 'menu/sort', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('17', '状态', '', '4', '5', 'menu/status', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('18', '显隐', '', '4', '6', 'menu/hide', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('19', '优化表', '', '5', '1', 'database/optimize', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('20', '修复表', '', '5', '2', 'database/repair', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('21', '删除备份', '', '6', '1', 'database/del', '0', '1', '0', '0', '0', '1');
INSERT INTO `go_menu` VALUES ('100', '用户管理', 'user', '0', '1', 'member/index', '0', '1', '0', '0', '1462419529', '1');
INSERT INTO `go_menu` VALUES ('101', '用户列表', 'group', '100', '1', 'member/index', '0', '1', '0', '0', '0', '1');
