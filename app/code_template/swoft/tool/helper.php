<?php

if(!function_exists('dd')){
    /**
     * @param $content
     * @return false|string
     */
    function dd($content)
    {
        var_dump($content);exit;
    }
}

if(!function_exists('returnFormat')){
    /**
     * 消息格式化
     * @param int $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    function returnFormat($code = 1, $data = [], $msg = ''){
        $data = [
            'status' => $code,
            'data' => $data,
            'msg' => $msg,
        ];
        return json_encode($data, 256);
    }
}

if(!function_exists('success')){
    /**
     * 成功消息
     * @param array $data
     * @param string $msg
     * @param int $code
     */
    function success($data = [], $msg = '', $code = 1)
    {
        die(returnFormat($code, $data, $msg));
    }
}

if(!function_exists('error')){
    /**
     * 失败消息
     * @param string $msg
     * @param array $data
     * @param int $code
     */
    function error($msg = '', $data = [], $code = 0)
    {
        die(returnFormat($code, $data, $msg));
    }
}
if(!function_exists('getPackageTempLate')){
    /**
     * 失败消息
     * @param $dir
     * @return false|string
     */
    function getPackageTempLate($dir)
    {
        $base = BASE_PATH . 'app/code_template/swoft/template/package/';
        $file = $base . $dir . '.txt';
        return file_get_contents($file);
    }
}

if(!function_exists('templateReplace')){
    /**
     * 该函数返回一个字符串或者数组。该字符串或数组是将 subject 中全部的 search 都被 replace 替换之后的结果
     * @param $search
     * @param $replace
     * @param $subject
     * @return false|string
     */
    function templateReplace($search, $replace, $subject ){
        if(is_array($search)){
            foreach ($search as $key => $item){
                $config = [
                    "$item" => "[% $item %]",
                ];
                $subject = str_replace($config[$item], $replace[$item], $subject);
            }

            return $subject;
        }else{
            $config = [
                "$search" => "[% $search %]",
            ];
            return str_replace($config[$search], $replace, $subject);
        }
    }
}

