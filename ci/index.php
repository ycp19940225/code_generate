<?php

fwrite(STDOUT, "输入代码根目录: ");

$dir = trim(fgets(STDIN));

$root = 'E:/phpstudy_pro/WWW/';

$baseDir = $root.$dir;

if(!file_exists($baseDir)){
    echo '当前项目不存在';
    $check = true;
    while ($check){
        fwrite(STDOUT, "\n输入代码根目录: ");
        $dir = trim(fgets(STDIN));
        $baseDir = $root.$dir;
        if(file_exists($baseDir)){
            $check = false;
        }else{
            echo "当前项目不存在";
        }
    }
}


$controlTemplate = './formwork/swoft-service-base/app/Http/Controller/DemoController.php';
$logicTemplate = './formwork/swoft-service-base/app/Model/Logic/DemoLogic.php';

$viewTemplate[] = './formwork/swoft-service-base/resource/demo/index.php';
$viewTemplate[] = './formwork/swoft-service-base/resource/demo/form_add.php';
$viewTemplate[] = './formwork/swoft-service-base/resource/demo/form_edit.php';

$tempDir = './temp/temp.php';
copy($controlTemplate, $tempDir);

fwrite(STDOUT, "输入模块名: ");
$module = trim(fgets(STDIN));
$controllerContent = file_get_contents($tempDir);

$class = ucfirst($module);
$controllerContentTemp = str_replace('Demo', $class, $controllerContent);
$controllerContentTemp = str_replace('demo', strtolower($module), $controllerContentTemp);

//处理字段
fwrite(STDOUT, "输入sql: ");
$fieldsArray = [];
while (true){
    $fieldsArray[] = $check = trim(fgets(STDIN));
    if(empty($check)){
        break;
    }
}

fwrite(STDOUT, "表单类型（1弹窗2页面）: ");
$formType = trim(fgets(STDIN));

$tableFields = $editFields = $viewFields = $viewFormFields = $viewFormJsFields = $viewFormValidateFields2 = [];
foreach ($fieldsArray as $item){
    $desc = explode(' ', $item);
    if(!empty($desc[0])){
        $str = str_replace('`', '', $desc[0]);
        $strName = searchStr($item);
        $tableFields[] = '                $data[\'aaData\'][$k][] = $r[\''.$str.'\'];';
        $editFields[] = '                \''.$str.'\' => $data[\''. $str .'\'],';
        $viewFields[] = str_repeat(' ', 11*4).'<th >'.$strName.'</th>';
        $viewFormFields[] = str_repeat(' ', 5*4).'<div class="form-group">
                        <label>'.$strName.'</label>
                        <input type="text" id="'.$str.'" name="'.$str.'" class="form-control" placeholder="请输入'.$strName.'"/>
                    </div>';
        $viewFormValidateFields[] = str_repeat(' ', 5*4).'<div class="form-group">
                        <label>'.$strName.'</label>
                        <input type="text" id="'.$str.'" name="'.$str.'" class="form-control" placeholder="请输入'.$strName.'"/>
                    </div>';
        $viewFormJsFields[] = str_repeat(' ', 2*4).''.$str.': {
            validators: {
                notEmpty: {
                    message: \'请选择'.$strName.'\'
                }
            }
        },';
        // 跳转表单
        $viewFormValidateFields2[] = str_repeat(' ', 10*4).'<div class="form-group col-lg-6">
                                            <label>'.$strName.'<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="'.$str.'" name="'.$str.'" class="form-control" placeholder="请输入'.$strName.'"/>
                                            </div>
                                        </div>';
    }
}
$tableFieldsTemplate = implode(PHP_EOL, $tableFields);
$controllerContentTemp = str_replace('// template_fields_start,template_fields_end', $tableFieldsTemplate, $controllerContentTemp);
// 替换编辑添加字段
$editFieldsTemplate = implode(PHP_EOL, $editFields);
$controllerContentTemp = str_replace('// template_edit_fields_start,template_edit_fields_end', $editFieldsTemplate, $controllerContentTemp);
$controllerContentTemp = str_replace('// template_add_fields_start,template_add_fields_end', $editFieldsTemplate, $controllerContentTemp);

$handle1 = '                $action .= get_icon(\'edit\', [], base_url(\'admin/demo/edit2?id=\'. $r[\'id\']));';
$handle2 = '                $action .= get_icon(\'edit\', [\'id\' => $r[\'id\'], \'operation\' => \'edit\']);';

if($formType == 1){
    $controllerContentTemp = str_replace('// template_handle_start,template_handle_end', $handle2, $controllerContentTemp);
    $editMethod = '     /**
     * 编辑
     * @RequestMapping()
     * @param Request $request
     * @param Response $response
     * @return array
     */
    public function edit(Request $request, Response $response)
    {
        $id = $request->input(\'id\', \'\');
        $data = DemoLogic::getInfo([\'id\' => $id]);
        return responseJson(1, \'成功!\', $data);
    }';
    $controllerContentTemp = str_replace('// template_edit_start,template_edit_end', $editMethod, $controllerContentTemp);
}else{
    $controllerContentTemp = str_replace('// template_handle_start,template_handle_end', $handle1, $controllerContentTemp);
    $editMethod = '     /**
     * 编辑
     * @RequestMapping()
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Throwable
     */
    public function edit(Request $request, Response $response)
    {
        $id = $request->input(\'id\', \'\');
        $data = [];
        if (!empty($id)) {
            $data = DemoLogic::getInfo([\'id\' => $id]);
            return view(\'demo/form_edit\', $data);
        }else{
            return view(\'demo/form_add\', $data);
        }
    }';

    $controllerContentTemp = str_replace('// template_edit_start,template_edit_end', $editMethod, $controllerContentTemp);
}

$controllerDir = "$baseDir\app\Http\Controller\Admin";
$file = "$controllerDir/$class"."Controller.php";
$path = dirname($file);
if(!file_exists($path)){
    mkdir($path, '0777', true);
}
file_put_contents($file, $controllerContentTemp);


// model
copy($logicTemplate, $tempDir);
$logicContent = file_get_contents($tempDir);

$logicContentContentTemp = str_replace('Demo', ucfirst($module), $logicContent);
$logicContentContentTemp = str_replace('demo', strtolower($module), $logicContentContentTemp);

$modelDir = "$baseDir\app\Model\Logic";
$file = "$modelDir/$class"."Logic.php";
$path = dirname($file);
if(!file_exists($path)){
    mkdir($path, '0777', true);
}
file_put_contents($file, $logicContentContentTemp);





function searchStr($str){
    $pattern = "/COMMENT\s?(.*?)$/";
    preg_match($pattern, $str, $matches);

    $res = $matches[1];
    $res = preg_replace('/(\'*)/', '', $res);
    $res = preg_replace('/(,)*/', '', $res);
    return $res;
}