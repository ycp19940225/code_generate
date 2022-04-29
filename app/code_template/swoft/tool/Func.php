<?php

namespace App\code_template\swoft\tool;

class Func{
    public static function searchStr($str){
        $pattern = "/COMMENT\s?(.*?)$/";
        preg_match($pattern, $str, $matches);
        $res = $matches[1];
        $res = preg_replace('/(\'*)/', '', $res);
        $res = preg_replace('/(,)*/', '', $res);
        return $res;
    }

    public static function cmdInput($msg = ''){
        if(!empty($msg)){
            fwrite(STDOUT, $msg);
        }
        return trim(fgets(STDIN));
    }

    public static function templateReplace($str){
        $config = [
            'template_mark' => '[% mark %]',
        ];
        return str_replace('mark', $str, $config['template_mark']);
    }

    public static function getBaseDir($str){
        $base = str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/";
        return $base . $str;
    }
}