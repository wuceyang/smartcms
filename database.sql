/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.26 : Database - smart_cms
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`smart_cms` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `smart_cms`;

/*Table structure for table `cms_adv_category` */

DROP TABLE IF EXISTS `cms_adv_category`;

CREATE TABLE `cms_adv_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adv_title` varchar(16) NOT NULL COMMENT '广告位名称',
  `adv_num` int(11) NOT NULL DEFAULT '0' COMMENT '广告数量',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '当前时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态,1:正常,2:禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cms_adv_category` */

/*Table structure for table `cms_advertisement` */

DROP TABLE IF EXISTS `cms_advertisement`;

CREATE TABLE `cms_advertisement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL COMMENT '链接地址',
  `logopath` varchar(100) NOT NULL DEFAULT '' COMMENT '链接logo',
  `createdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '广告状态,1:正常,2:删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cms_advertisement` */

/*Table structure for table `cms_content` */

DROP TABLE IF EXISTS `cms_content`;

CREATE TABLE `cms_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL COMMENT '文章所属栏目ID，对应category表的ID字段',
  `title` varchar(200) NOT NULL COMMENT '文章标题',
  `summary` varchar(1024) NOT NULL COMMENT '摘要',
  `author` varchar(32) NOT NULL COMMENT '作者姓名',
  `source` varchar(32) NOT NULL COMMENT '来源',
  `keyword` varchar(32) NOT NULL COMMENT '关键字',
  `template_id` int(11) NOT NULL COMMENT '模板ID，对应template表的ID字段',
  `content` text NOT NULL COMMENT '文章正文',
  `type_id` tinyint(4) NOT NULL COMMENT '文章类型,对应content_type表的ID',
  `create_uid` int(11) NOT NULL COMMENT '创建的管理员ID',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '录入日期',
  `modify_uid` int(11) NOT NULL COMMENT '最后修改的管理员ID',
  `modify_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最后修改时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '文章状态,1:正常,2:删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cms_content` */

/*Table structure for table `cms_content_category` */

DROP TABLE IF EXISTS `cms_content_category`;

CREATE TABLE `cms_content_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(32) NOT NULL COMMENT '分类名称',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父分类ID',
  `parent_path` varchar(1024) NOT NULL DEFAULT ',0,' COMMENT '分类路径，查询分类下所有分类使用',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_uid` int(11) NOT NULL COMMENT '创建者用户ID',
  `content_num` int(11) NOT NULL DEFAULT '0' COMMENT '分类下的文章总数',
  `tplid` int(11) NOT NULL DEFAULT '0' COMMENT '本分类默认状态下使用的模板',
  `show_order` tinyint(4) NOT NULL DEFAULT '1' COMMENT '排序顺序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '分类状态,1:正常,2:禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `cms_content_category` */

insert  into `cms_content_category`(`id`,`category_name`,`parent_id`,`parent_path`,`create_time`,`create_uid`,`content_num`,`tplid`,`show_order`,`status`) values (1,'科技新闻',0,',0,','2016-11-20 23:39:27',1,0,0,2,2),(2,'太空探索',1,',0,1,','2016-11-20 23:49:40',1,0,0,1,1),(3,'历史考古',1,',0,1,','2016-11-20 23:49:57',1,0,0,2,1),(4,'娱乐新闻',0,',0,','2016-11-20 23:50:09',1,0,0,1,1),(5,'影视周边',4,',0,4,','2016-11-20 23:50:39',1,0,0,1,1),(6,'网络游戏',4,',0,4,','2016-11-20 23:51:10',1,0,0,2,1),(7,'互联网技术',1,',0,1,','2016-11-21 08:43:32',1,0,0,3,1),(8,'明星八卦',4,',0,4,','2016-11-21 08:46:27',1,0,0,3,1),(9,'社会万象',0,',0,','2016-11-21 21:22:03',1,0,0,3,1);

/*Table structure for table `cms_content_type` */

DROP TABLE IF EXISTS `cms_content_type`;

CREATE TABLE `cms_content_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(32) NOT NULL COMMENT '类型名称，如:套图，视频，普通文本等',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型状态，1:正常,2:禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cms_content_type` */

/*Table structure for table `cms_group_privileges` */

DROP TABLE IF EXISTS `cms_group_privileges`;

CREATE TABLE `cms_group_privileges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL COMMENT '分组ID，对应smart_user_group表的ID字段',
  `menu_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '可以管理的菜单ID列表，用英文逗号连接',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户分组和后台菜单关系表';

/*Data for the table `cms_group_privileges` */

insert  into `cms_group_privileges`(`id`,`group_id`,`menu_id`) values (1,1,'1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18'),(2,2,'25,29');

/*Table structure for table `cms_menu` */

DROP TABLE IF EXISTS `cms_menu`;

CREATE TABLE `cms_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '链接地址',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父菜单ID',
  `show_order` int(11) NOT NULL DEFAULT '1' COMMENT '在当前分类中的排序顺序',
  `level` int(11) NOT NULL DEFAULT '1' COMMENT '菜单级别',
  `icon` varchar(16) NOT NULL DEFAULT '' COMMENT '菜单图标样式',
  `enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用,1:启用,0:禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理后台菜单列表';

/*Data for the table `cms_menu` */

insert  into `cms_menu`(`id`,`title`,`url`,`parent_id`,`show_order`,`level`,`icon`,`enable`) values (1,'广告位管理','',0,1,1,'show',1),(2,'推荐位管理','',0,2,1,'nav',1),(3,'栏目管理','',0,3,1,'product',1),(4,'权限管理','',0,4,1,'manager',1),(5,'系统设置','',0,5,1,'system',1),(6,'内容管理','',0,6,1,'page',1),(7,'广告位列表','/admin/adv/index',1,1,2,'',1),(8,'新建广告位','/admin/adv/add-new',1,2,2,'',1),(9,'推荐位管理','/admin/recommend/index',2,1,2,'',1),(10,'新建推荐位','/admin/recommend/add-new',2,2,2,'',1),(11,'栏目列表','/admin/category/index',3,1,2,'',1),(12,'新建栏目','/admin/category/add-new',3,2,2,'',1),(13,'用户列表','/admin/user/all-user',4,1,2,'',1),(14,'用户分组','/admin/user-group/index',4,2,2,'',1),(15,'后台菜单列表','/admin/menu/index',4,3,2,'',1),(16,'模板管理','/admin/template/index',5,1,2,'',1),(17,'文章列表','/admin/content/index',6,1,2,'',1),(18,'文章录入','/admin/content/add-new',6,2,2,'',1);

/*Table structure for table `cms_recommend` */

DROP TABLE IF EXISTS `cms_recommend`;

CREATE TABLE `cms_recommend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '推荐位名称',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_uid` int(11) NOT NULL COMMENT '创建人用户ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:正常,2:禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `cms_recommend` */

insert  into `cms_recommend`(`id`,`name`,`create_time`,`create_uid`,`status`) values (1,'首页头条','2016-11-17 22:51:14',1,1),(2,'科技头条2','2016-11-17 22:51:56',1,2);

/*Table structure for table `cms_recommend_content` */

DROP TABLE IF EXISTS `cms_recommend_content`;

CREATE TABLE `cms_recommend_content` (
  `rid` int(10) unsigned NOT NULL COMMENT '推荐位ID',
  `cid` int(10) unsigned NOT NULL COMMENT '文章ID',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `create_uid` int(11) NOT NULL COMMENT '添加用户ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cms_recommend_content` */

/*Table structure for table `cms_template` */

DROP TABLE IF EXISTS `cms_template`;

CREATE TABLE `cms_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(32) NOT NULL COMMENT '模板名称，一定为非空，并且不同，方便后面指定模板的时候区分',
  `template_html` text NOT NULL COMMENT '模板内容正文',
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '模板状态，1:正常,2:删除',
  `create_uid` int(11) NOT NULL COMMENT '上传者用户ID',
  `modify_uid` int(11) NOT NULL DEFAULT '0' COMMENT '更改的用户ID',
  `modify_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `cms_template` */

insert  into `cms_template`(`id`,`template_name`,`template_html`,`create_date`,`status`,`create_uid`,`modify_uid`,`modify_time`) values (1,'轮播图','<div class=\"mod_right_focus\">\r\n                        <div ne-module=\"\">\r\n<div class=\"mod_focus\" ne-module=\"/modules/slide/slide.js\" ne-state=\"slideMethod:left;events=mouseover;interval=4000;loop=true;\">\r\n    <div class=\"f_body\" ne-role=\"slide-body\">\r\n        <ul class=\"f_main clearfix\" ne-role=\"slide-scroll\" style=\"width: 1200px; position: relative; left: 0px;\">\r\n                                    <li ne-role=\"slide-page\" class=\"current\" style=\"width: 300px; left: 0px;\">\r\n                <a href=\"http://news.163.com/photoview/00AP0001/2214925.html\">\r\n                    <img src=\"http://imgsize.ph.126.net/?imgurl=http://cms-bucket.nosdn.127.net/702c10f69c564a42b74dae2eee4025f620161125080334.jpeg_300x400x1x85.jpg\" width=\"300\" height=\"400\">\r\n                    <span class=\"bg\"></span>\r\n                    <h3>沈阳毕业生双选会万余人参加</h3>\r\n                </a>\r\n            </li>\r\n                        <li ne-role=\"slide-page\" class=\"\" style=\"width: 300px;\">\r\n                <a href=\"http://g.163.com/a?CID=37121&amp;Values=30862994&amp;Redirect=http://rd.da.netease.com/redirect?t=oxdmuYsBUX&amp;p=XqSA54&amp;proId=1922&amp;target=http%3A%2F%2Fwww.kaola.com%2Factivity%2Fdetail%2F20422.shtml%3FshowNovicePop%3Dkaola8182%26tag%3Dbe3d8d027a530881037ef01d304eb505\">\r\n                    <img src=\"http://imgsize.ph.126.net/?imgurl=http://img1.126.net/channel14/news_300400_1125.jpg_300x400x1x85.jpg\" width=\"300\" height=\"400\">\r\n                    <span class=\"bg\"></span>\r\n                    <h3>网易黑猪肉首发1元起拍</h3>\r\n                </a>\r\n            </li>\r\n                        <li ne-role=\"slide-page\" style=\"width: 300px;\" class=\"\">\r\n                <a href=\"http://news.163.com/photoview/00AO0001/2214910.html\">\r\n                    <img src=\"http://imgsize.ph.126.net/?imgurl=http://cms-bucket.nosdn.127.net/f03903cbaef74aec8cfe83ca0a15202120161125080551.jpeg_300x400x1x85.jpg\" width=\"300\" height=\"400\">\r\n                    <span class=\"bg\"></span>\r\n                    <h3>IS放火烧油田 儿童浓烟中踢球</h3>\r\n                </a>\r\n            </li>\r\n                        <li ne-role=\"slide-page\" style=\"width: 300px;\" class=\"\">\r\n                <a href=\"http://auto.163.com/photoview/5BD20008/189735.html\">\r\n                    <img src=\"http://imgsize.ph.126.net/?imgurl=http://cms-bucket.nosdn.127.net/84949f57b6fb43d782f7819e5f8f993420161125092311.jpeg_300x400x1x85.jpg\" width=\"300\" height=\"400\">\r\n                    <span class=\"bg\"></span>\r\n                    <h3>预售27万起 大众新Tiguan今上市</h3>\r\n                </a>\r\n            </li>\r\n                    </ul>\r\n    </div>\r\n    <div class=\"f_title\">\r\n                        <span ne-role=\"slide-nav\" class=\"current\">1</span>\r\n                <span ne-role=\"slide-nav\" class=\"\">2</span>\r\n                <span ne-role=\"slide-nav\" class=\"\">3</span>\r\n                <span ne-role=\"slide-nav\" class=\"\">4</span>\r\n            </div>\r\n    <a ne-role=\"slide-prev\" class=\"f_prev\">&lt;</a>\r\n    <a ne-role=\"slide-next\" class=\"f_next\">&gt;</a>\r\n</div>\r\n</div>\r\n                    </div>','2016-11-25 14:30:30',1,1,1,'2016-11-25 15:21:47'),(2,'视频','啊大大撒地方','2016-11-25 15:21:57',1,1,1,'2016-11-25 15:22:04');

/*Table structure for table `cms_user` */

DROP TABLE IF EXISTS `cms_user`;

CREATE TABLE `cms_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户真实姓名',
  `passwd` varchar(32) NOT NULL DEFAULT '' COMMENT '登录密码',
  `account` varchar(21) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `avatar` varchar(128) NOT NULL DEFAULT 'http://cdn-img.easyicon.net/png/10719/1071999.gif' COMMENT '用户头像',
  `reg_time` int(11) NOT NULL COMMENT '注册时间',
  `group_id` varchar(128) NOT NULL COMMENT '用户所在分组ID,对应user_group表的id,区分所属角色用，并用来识别权限',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '帐号状态，1:正常,2:禁用,',
  `login_times` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数统计',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='后台用户表';

/*Data for the table `cms_user` */

insert  into `cms_user`(`id`,`username`,`passwd`,`account`,`avatar`,`reg_time`,`group_id`,`status`,`login_times`) values (1,'张三','6266d4f13e622e81860359287e990922','admin','http://cdn-img.easyicon.net/png/10719/1071999.gif',2,'2,1',1,0),(2,'李思','d563d1c07254858515d26b39b07d9073','lisi','',1471066480,'2',1,0),(3,'王五','d93940e39352086101adf930ceed9e4d','wangwu','',1471066521,'1',1,0),(4,'赵六','d55193af9bc6647d9c341259afa6fc53','zhaoliu','',1471066698,'2',1,0),(5,'钱七','eb9e9d55d1811d37ac7088b3e4af9f80','qianqi','',1471066823,'2',1,0);

/*Table structure for table `cms_user_group` */

DROP TABLE IF EXISTS `cms_user_group`;

CREATE TABLE `cms_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '分组名称',
  `create_dateline` int(11) NOT NULL COMMENT '分组创建时间',
  `create_by` int(11) NOT NULL COMMENT '此分组创建者用户ID',
  `group_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分组状态,1:正常,2:禁用',
  `is_fixed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否固定分组(不能删除或者编辑),0:非固定分组,1:固定分组',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户分组对应关系表';

/*Data for the table `cms_user_group` */

insert  into `cms_user_group`(`id`,`group_name`,`create_dateline`,`create_by`,`group_status`,`is_fixed`) values (1,'管理员',1469174151,1,1,1),(2,'操作员',1469174151,1,1,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
