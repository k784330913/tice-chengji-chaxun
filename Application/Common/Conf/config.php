<?php
return array(
	//'配置项'=>'配置值'
	'MODULE_ALLOW_LIST' => array('Home','Admin'),
	'DEFAULT_MODULE' => 'Home',
	'URL_PARAMS_BIND' => true, // URL变量绑定到操作方法作为参数
	'URL_HTML_SUFFIX'=>'html',
	//默认错误跳转对应的模板文件
	//'TMPL_ACTION_ERROR' => 'Public:error',
	//默认成功跳转对应的模板文件
	//'TMPL_ACTION_SUCCESS' => 'Public:success'
	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => '127.0.0.1', // 服务器地址
	'DB_NAME' => 'test', // 数据库名
	'DB_USER' => 'root', // 用户名
	'DB_PWD' => 'admin', // 密码
	'DB_PORT' => '3306', // 端口
	'DB_PREFIX' => '', // 数据库表前缀
	'DB_DSN' => '', // 数据库连接DSN 用于PDO方式
	'DB_CHARSET' => 'utf8', // 数据库的编码 默认为utf8
);
