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

if($formType == 1){
    foreach ($viewTemplate as $item){
        copy($item, $tempDir);
        $viewContentTemp = file_get_contents($tempDir);


        // 替换view表格字段
        $viewFieldsTemplate = implode(PHP_EOL, $viewFields);
        $viewContentTemp = str_replace('<!--template_view_fields_start,template_view_fields_end-->', $viewFieldsTemplate, $viewContentTemp);

        // 替换表单
        $formTemplate = '<div class="modal fade" id="preserve_modal" tabindex="-1" role="dialog" aria-labelledby="preserve_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="preserve_form">
                <div class="modal-header">
                    <h5 class="modal-title" id="preserve_title">添加</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
<!--template_form_fields_start,template_form_fields_end-->
                    <div class="form-group">
                        <label>名称</label>
                        <input type="number" id="name" name="name" class="form-control" placeholder="请输入名称"/>
                    </div>
                    <div class="form-group">
                        <label>状态 <span class="text-danger">*</span></label>
                        <select class="form-control selectpicker" id="status2" name="status" data-show-tick="true" data-live-search="false" title="请选择状态">
                            <option value="0">隐藏</option>
                            <option value="1" selected>显示</option>
                            <option value="2">测试</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id" value="">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">关闭</button>
                    <button type="button" id="preserve_form_submit" class="btn btn-primary font-weight-bold">确定</button>
                </div>
            </form>
        </div>
    </div>
</div>';

        // 替换字段
        $viewFormFieldsTemplate = implode(PHP_EOL, $viewFormFields);
        $viewForm = str_replace('<!--template_form_fields_start,template_form_fields_end-->', $viewFormFieldsTemplate, $formTemplate);
        $viewContentTemp = str_replace('<!--template_form_start,template_form_end-->', $viewForm, $viewContentTemp);

        $formInfo = '    //窗口添加
    let preserve_modal = $(\'#preserve_modal\');
    let preserve_form = $(\'#preserve_form\');
    $(document).on(\'click\', \'#preserve\', function () {
        reset_form(preserve_form);
        $(\'#preserve_title\').html(\'添加\');
        $("#id").val(\'\');
        preserve_modal.modal();
    });

    //添加验证器
    validation = form_validation(\'preserve_form\', {
<!--template_validate_start,template_validate_end-->
        status: {
            validators: {
                notEmpty: {
                    message: \'请选择状态\'
                }
            }
        },
    });
    var addStatus = 1;
    $(\'#preserve_form_submit\').on(\'click\', function (e) {
        e.preventDefault();
        if(addStatus){
            validation.validate().then(function (status) {
                if (status === \'Valid\') {
                    addStatus = false;
                    let url = "<?php echo base_url(\'admin/demo/editDo\'); ?>";
                    let params = serialize_object(preserve_form);
                    $.axios.request(\'POST\', url, params, function (response_data) {
                        if(response_data.status != 1) {
                            setTimeout(function () {
                                addStatus = 1;
                            }, 1500)
                        }
                        showMessage(response_data);
                    });
                }
            })
        }
    })
    //编辑
    $(document).on(\'click\', \'[data-operation="edit"]\', function () {
        let key = $(this).data(\'id\');

        //获取管理员信息
        let url = "<?php echo base_url(\'admin/demo/edit\'); ?>";
        let params = {
            id: key
        };
        $.axios.request(\'POST\', url, params, function (response_data) {
            $(\'#preserve_title\').html(\'编辑\');
            reset_form(preserve_form);

            let data = response_data.data;
            <!--template_form_info_start-->
            preserve_form.find("input[name=\'id\']").val(data.id);
            preserve_form.find("input[name=\'name\']").val(data.name);
            <!--template_form_info_end-->
            preserve_form.find("select[name=\'status\']").selectpicker(\'val\', data.status);
            preserve_modal.modal();
        });
    });';

        // 替换字段
        $viewFormJsTemplate = implode(PHP_EOL, $viewFormJsFields);
        $viewJsForm = str_replace('<!--template_validate_start,template_validate_end-->', $viewFormJsTemplate, $formInfo);
        $viewContentTemp = str_replace('<!--template_form_js_start,template_form_js_end-->', $viewJsForm, $viewContentTemp);


        // 最后统一替换模块名称
        $viewContentTemp = str_replace('Demo', ucfirst($module), $viewContentTemp);
        $viewContentTemp = str_replace('demo', strtolower($module), $viewContentTemp);


        $viewDir = "$baseDir\/resource";
        $fileName = explode('/', $item);
        $fileName = array_pop($fileName);
        $file = "$viewDir/$module/$fileName";
        $path = dirname($file);
        if(!file_exists($path)){
            mkdir($path, '0777', true);
        }
        file_put_contents($file, $viewContentTemp);
        break;
    }
}else{
    foreach ($viewTemplate as $item){
        copy($item, $tempDir);
        $viewContentTemp = file_get_contents($tempDir);

        // 替换view表格字段
        $viewFieldsTemplate = implode(PHP_EOL, $viewFields);
        $viewContentTemp = str_replace('<!--template_view_fields_start,template_view_fields_end-->', $viewFieldsTemplate, $viewContentTemp);


        // 替换form字段
        $viewFieldsTemplate = implode(PHP_EOL, $viewFormValidateFields2);
        $viewContentTemp = str_replace('<!--template_form_filed_start,template_form_filed_end-->', $viewFieldsTemplate, $viewContentTemp);

        // 替换form_js字段
        $viewFieldsTemplate = implode(PHP_EOL, $viewFormJsFields);
        $viewContentTemp = str_replace('<!--template_validate_start,template_validate_end-->', $viewFieldsTemplate, $viewContentTemp);


        $viewContentTemp = str_replace('Demo', ucfirst($module), $viewContentTemp);
        $viewContentTemp = str_replace('demo', strtolower($module), $viewContentTemp);

        $viewDir = "$baseDir\/resource";
        $fileName = explode('/', $item);
        $fileName = array_pop($fileName);
        $file = "$viewDir/$module/$fileName";
        $path = dirname($file);
        if(!file_exists($path)){
            mkdir($path, '0777', true);
        }
        file_put_contents($file, $viewContentTemp);
    }
}



function searchStr($str){
    $pattern = "/COMMENT\s?(.*?)$/";
    preg_match($pattern, $str, $matches);

    $res = $matches[1];
    $res = preg_replace('/(\'*)/', '', $res);
    $res = preg_replace('/(,)*/', '', $res);
    return $res;
}