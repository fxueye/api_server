<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//项目自定义配置项
$config['app_key'] = getenv("PROJECT_KEY");
$config['app_iv'] = getenv("PROJECT_IV");

$config['project_env'] = getenv("CI_ENV");
$config['open_id_key'] = getenv("OPEN_KEY");

$config['tao_app_key'] = getenv("TAO_APP_KEY");
$config['tao_app_secret']=getenv("TAO_APP_SECRET");

$config['report_path'] = getenv("PROJECT_REPORT_LOG_PATH");

$config['tao_pid'] = getenv("TAO_PID");

// $config['app_key'] = "aaaaaaaaaaaaaaaa";
// $config['app_iv'] = "aaaaaaaaaaaaaaaa";

// $config['tao_app_key'] = "23861075";
// $config['tao_app_secret']="ca352db1f70762070b63a4d64cf4439d";

// $config['project_env'] = "development";
// $config['open_id_key'] = "XI392ksl83LE2m";

// $config['report_path'] = "./log/";