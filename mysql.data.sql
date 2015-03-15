CREATE TABLE `sb_sys_cli_runtime_error` (
  `sbx_sys_cli_runtime_error_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `file` varchar(255) DEFAULT '',
  `line` int(11) unsigned DEFAULT NULL,
  `error_type` int(11) unsigned NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `server_name` varchar(100) DEFAULT NULL,
  `execution_script` varchar(255) NOT NULL DEFAULT '',
  `pid` int(11) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(16) DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`sbx_sys_cli_runtime_error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `sb_sys_cli_query_error` (
  `sbx_sys_cli_query_error_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `query` text,
  `file` varchar(255) DEFAULT '',
  `line` int(11) unsigned DEFAULT NULL,
  `error_string` varchar(255) DEFAULT '',
  `error_no` int(11) unsigned DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `execution_script` varchar(255) DEFAULT '',
  `pid` int(11) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(16) DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`sbx_sys_cli_query_error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sb_sys_cli_task` (
  `task_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `script_name` varchar(255) NOT NULL DEFAULT '',
  `params` varchar(255) DEFAULT '',
  `server_name` varchar(30) DEFAULT '',
  `server_user` varchar(30) DEFAULT '',
  `start_time` datetime DEFAULT NULL,
  `stop_time` datetime DEFAULT NULL,
  `state` enum('RUNNING','SUCCESSFUL','FAILED') DEFAULT 'RUNNING',
  `exit_status` int(11) unsigned DEFAULT NULL,
  `stdout` text,
  `stderr` text,
  `pid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
