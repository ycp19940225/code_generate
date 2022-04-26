<?php

function searchStr($str){
    $pattern = "/COMMENT\s?(.*?)$/";
    preg_match($pattern, $str, $matches);
    $res = $matches[1];
    $res = preg_replace('/(\'*)/', '', $res);
    $res = preg_replace('/(,)*/', '', $res);
    return $res;
}

function cmdInput($msg = ''){
    if(!empty($msg)){
        fwrite(STDOUT, $msg);
    }
    return trim(fgets(STDIN));
}

function templateReplace($str){
    $config = [
        'template_mark' => '[% mark %]',
    ];
    return str_replace('mark', $str, $config['template_mark']);
}