<?php

$root = './gen_code';

$baseDir = $root;

$controlTemplate = './template/controller/Demo.php';
$logicTemplate = './template/model/DemoModel.php';

$viewTemplate = './template/view/index.php';

$tempDir = './temp/temp.php';
copy($controlTemplate, $tempDir);

fwrite(STDOUT, "输入模块名: ");

$module = trim(fgets(STDIN));

//处理字段
fwrite(STDOUT, "输入sql: ");
$fieldsArray = [];
while (true){
    $fieldsArray[] = $check = trim(fgets(STDIN));
    if(empty($check)){
        break;
    }
}

$tableFields = $editFields = $viewFields = $viewFormFields = $viewFormJsFields = $viewFormEditFields = [];
foreach ($fieldsArray as $item){
    $desc = explode(' ', $item);
    if(!empty($desc[0])){
        $str = str_replace('`', '', $desc[0]);
        $strName = searchStr($item);
        $tableFields[] = str_repeat(' ', 4*4).'$data[\'aaData\'][$key][] = $datum[\''.$str.'\'];';
        $editFields[] = str_repeat(' ', 2*4).'\''.$str.'\' => $data[\''. $str .'\'],';
        $viewFields[] = str_repeat(' ', 4*4).'<th >'.$strName.'</th>';
        $viewFormFields[] = str_repeat(' ', 5*4).'<div class="form-group">
                        <label for="hori-pass1" class="col-sm-2 control-label">'.$strName.'</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" required id="'.$str.'" name="'.$str.'" placeholder="请输入'.$strName.'"/>
                        </div>
                    </div>';
        $viewFormEditFields[] = str_repeat(' ', 4*4).'$(demo_form).find(\'[name="'.$str.'"]\').val(data.data.'.$str.');';
    }
}

// --------------------------------------------controller--------------------------------------------------------------
$controllerContentTemp = file_get_contents($tempDir);
$class = ucfirst($module);
$tableFieldsTemplate = implode(PHP_EOL, $tableFields);
$controllerContentTemp = str_replace('// template_fields_start,template_fields_end', $tableFieldsTemplate, $controllerContentTemp);

$controllerContentTemp = str_replace('Demo', $class, $controllerContentTemp);
$controllerContentTemp = str_replace('demo', strtolower($module), $controllerContentTemp);


$controllerDir = "$baseDir\controller";
if(!file_exists($controllerDir)){
    mkdir($controllerDir, '0777', true);
}
$file = "$controllerDir/$class".".php";
$path = dirname($file);
if(!file_exists($path)){
    mkdir($path, '0777', true);
}
file_put_contents($file, $controllerContentTemp);


// model
copy($logicTemplate, $tempDir);
$logicContent = file_get_contents($tempDir);

// 替换编辑添加字段
$editFieldsTemplate = implode(PHP_EOL, $editFields);
$controllerContentTemp = str_replace('// template_edit_fields_start,template_edit_fields_end', $editFieldsTemplate, $logicContent);


$logicContentContentTemp = str_replace('Demo', ucfirst($module), $controllerContentTemp);
$logicContentContentTemp = str_replace('demo', strtolower($module), $logicContentContentTemp);


$modelDir = "$baseDir\model";
if(!file_exists($modelDir)){
    mkdir($modelDir, '0777', true);
}
$file = "$modelDir/$class"."Model.php";
$path = dirname($file);
if(!file_exists($path)){
    mkdir($path, '0777', true);
}
file_put_contents($file, $logicContentContentTemp);

copy($viewTemplate, $tempDir);
$viewContentTemp = file_get_contents($tempDir);

// 替换view表格字段
$viewFieldsTemplate = implode(PHP_EOL, $viewFields);
$viewContentTemp = str_replace('<!--template_view_fields_start,template_view_fields_end-->', $viewFieldsTemplate, $viewContentTemp);


// 替换form字段
$viewFieldsTemplate = implode(PHP_EOL, $viewFormFields);
$viewContentTemp = str_replace('<!--template_form_filed_start,template_form_filed_end-->', $viewFieldsTemplate, $viewContentTemp);


//替换编辑时赋值
$viewFieldsEditTemplate = implode(PHP_EOL, $viewFormEditFields);
$viewContentTemp = str_replace('<!--template_form_edit_start,template_form_edit_end-->', $viewFieldsEditTemplate, $viewContentTemp);

$viewContentTemp = str_replace('Demo', ucfirst($module), $viewContentTemp);
$viewContentTemp = str_replace('demo', strtolower($module), $viewContentTemp);

$viewDir = "$baseDir\/view";
if(!file_exists($viewDir)){
    mkdir($viewDir, '0777', true);
}
$file = "$viewDir/index.php";
file_put_contents($file, $viewContentTemp);


function searchStr($str){
    $pattern = "/COMMENT\s?(.*?)$/";
    preg_match($pattern, $str, $matches);

    $res = $matches[1];
    $res = preg_replace('/(\'*)/', '', $res);
    $res = preg_replace('/(,)*/', '', $res);
    return $res;
}




