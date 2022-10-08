<?php
require_once dirname(__DIR__) . '/code_generate/vendor/autoload.php';

define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
// Run application
(new \App\handle\Application())->run();