<?php
include_once './tool/func.php';
//var_dump(templateReplace('title'));exit;
$baseDir = './gen_code';
$controlTemplate = './template/controller/DemoController.php';
$logicTemplate = './template/logic/DemoLogic.php';

$viewTemplate[] = './template/view/index.php';
$viewTemplate[] = './template/view/form_add.php';
$viewTemplate[] = './template/view/form_edit.php';

$tempDir = './temp/temp.php';
copy($controlTemplate, $tempDir);

$module = cmdInput("输入模块名: ");
$controllerContent = file_get_contents($tempDir);

$moduleTemp = explode('_', $module);
$class = [];
foreach ($moduleTemp as $item){
    $class[] = ucfirst($item);
}
$class = implode('', $class);
$controllerContentTemp = str_replace('Demo', $class, $controllerContent);
$controllerContentTemp = str_replace('demo', strtolower($module), $controllerContentTemp);
$ConTroModule = cmdInput("请选择输入控制器模块: ");
if(!empty($ConTroModule)){
    $ConTroModule = ucfirst($ConTroModule);
    $strTemp = "namespace App\Http\Controller\\$ConTroModule;";
    $controllerContentTemp = str_replace('namespace App\Http\Controller;', $strTemp, $controllerContentTemp);
}

//处理字段
fwrite(STDOUT, "输入sql: ");
$fieldsArray = [];
while (true){
    $fieldsArray[] = $check = trim(fgets(STDIN));
    if(empty($check)){
        break;
    }
}

$formType = cmdInput("请选择表单类型（1弹窗2页面）: ");

$title = cmdInput("请输入页面标题: ");

if(!empty($title)){
    $controllerContentTemp = str_replace(templateReplace('title'), $title, $controllerContentTemp);
}
// 替换title

$tableFields = $editFields = $viewFields = $viewFormFields = $viewFormJsFields = $viewFormValidateFields = [];
foreach ($fieldsArray as $item){
    $desc = explode(' ', $item);
    if(!empty($desc[0])){
        $str = str_replace('`', '', $desc[0]);
        $strName = searchStr($item);
        // 列表字段
        $tableFields[] = '                $data[\'aaData\'][$k][] = $r[\''.$str.'\'];';
        // 编辑字段
        $editFields[] = '                \''.$str.'\' => $data[\''. $str .'\'],';
        // 表格字段
        $viewFields[] = str_repeat(' ', 11*4).'<th >'.$strName.'</th>';
        // 表单字段
        $viewFormFields[] = str_repeat(' ', 5*4).'<div class="form-group">
                        <label>'.$strName.'</label>
                        <input type="text" id="'.$str.'" name="'.$str.'" class="form-control" placeholder="请输入'.$strName.'"/>
                    </div>';
        // 表单验证字段
        $viewFormJsFields[] = str_repeat(' ', 2*4).''.$str.': {
            validators: {
                notEmpty: {
                    message: \'请选择'.$strName.'\'
                }
            }
        },';
        $viewFormEditFields[] = str_repeat(' ', 3*4).'preserve_form.find("input[name=\''.$str.'\']").val(data.'.$str.');';
        // 跳转表单
        $viewFormValidateFields[] = str_repeat(' ', 10*4).'<div class="form-group col-lg-6">
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

$handle1 = '                $action .= get_icon(\'edit\', [], base_url(\'admin/demo/edit?id=\'. $r[\'id\']));';
$handle1 = str_replace('demo', strtolower($module), $handle1);
$handle2 = '                $action .= get_icon(\'edit\', [\'id\' => $r[\'id\'], \'operation\' => \'edit\']);';


/***************************************************************controller***************************************************************/
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
    $editMethod = str_replace('Demo', $class, $editMethod);
    $editMethod = str_replace('demo', $class, $editMethod);
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
    $editMethod = str_replace('Demo', $class, $editMethod);
    $editMethod = str_replace('demo', $class, $editMethod);
    $controllerContentTemp = str_replace('// template_edit_start,template_edit_end', $editMethod, $controllerContentTemp);
}

$controllerDir = "$baseDir\app\Http\Controller\Admin";
$file = "$controllerDir/$class"."Controller.php";
$path = dirname($file);
if(!file_exists($path)){
    mkdir($path, '0777', true);
}
file_put_contents($file, $controllerContentTemp);


/***************************************************************model***************************************************************/
copy($logicTemplate, $tempDir);
$logicContent = file_get_contents($tempDir);

$logicContentContentTemp = str_replace('Demo', $class, $logicContent);
$logicContentContentTemp = str_replace('demo', strtolower($module), $logicContentContentTemp);

$modelDir = "$baseDir\app\Model\Logic";
$file = "$modelDir/$class"."Logic.php";
$path = dirname($file);
if(!file_exists($path)){
    mkdir($path, '0777', true);
}
file_put_contents($file, $logicContentContentTemp);


/***************************************************************view***************************************************************/
if($formType == 1){
    foreach ($viewTemplate as $item){
        copy($item, $tempDir);
        $viewContentTemp = file_get_contents($tempDir);
        // 替换操作按钮
        $button = '<button type="button" class="btn btn-primary" id="preserve">
                                                    <i class="la la-plus-circle"></i> 添加
                                                </button>';
        $viewContentTemp = str_replace('<!--template_button_start,template_button_end-->', $button, $viewContentTemp);

        // 替换title
        $viewContentTemp = str_replace(templateReplace('title'), $title, $viewContentTemp);

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
<!--template_form_info_start,template_form_info_end-->
            preserve_form.find("input[name=\'id\']").val(data.id);

            preserve_form.find("select[name=\'status\']").selectpicker(\'val\', data.status);
            preserve_modal.modal();
        });
    });';

        // 替换字段
        $viewFormJsTemplate = implode(PHP_EOL, $viewFormJsFields);
        $viewFormEditFields = implode(PHP_EOL, $viewFormEditFields);
        $viewJsForm = str_replace('<!--template_validate_start,template_validate_end-->', $viewFormJsTemplate, $formInfo);
        $viewJsForm = str_replace('<!--template_form_info_start,template_form_info_end-->', $viewFormEditFields, $viewJsForm);
        $viewContentTemp = str_replace('<!--template_form_js_start,template_form_js_end-->', $viewJsForm, $viewContentTemp);


        // 最后统一替换模块名称
        $viewContentTemp = str_replace('Demo', $class, $viewContentTemp);
        $viewContentTemp = str_replace('demo', strtolower($module), $viewContentTemp);


        $viewDir = "$baseDir\/resource";
        $fileName = explode('/', $item);
        $fileName = array_pop($fileName);
        $moduleTemp = $class;
        $file = "$viewDir/$moduleTemp/$fileName";
        $path = dirname($file);
        if(!file_exists($path)){
            mkdir($path, '0777', true);
        }
        file_put_contents($file, $viewContentTemp);
        break;
    }
}else{
    foreach ($viewTemplate as $key => $item){
        copy($item, $tempDir);
        $viewContentTemp = file_get_contents($tempDir);

        if($key == 0){
            // 替换操作按钮
            $button = '<a href="<?php echo base_url(\'admin/demo/edit\');?>" type="button" class="btn btn-primary">
                                                    <i class="la la-plus-circle"></i> 添加
                                                </a>';
            $viewContentTemp = str_replace('<!--template_button_start,template_button_end-->', $button, $viewContentTemp);
        }

        // 替换title
        $viewContentTemp = str_replace(templateReplace('title'), $title, $viewContentTemp);

        // 替换view表格字段
        $viewFieldsTemplate = implode(PHP_EOL, $viewFields);
        $viewContentTemp = str_replace('<!--template_view_fields_start,template_view_fields_end-->', $viewFieldsTemplate, $viewContentTemp);


        // 替换form字段
        $viewFieldsTemplate = implode(PHP_EOL, $viewFormValidateFields);
        $viewContentTemp = str_replace('<!--template_form_filed_start,template_form_filed_end-->', $viewFieldsTemplate, $viewContentTemp);

        // 替换form_js字段
        $viewFieldsTemplate = implode(PHP_EOL, $viewFormJsFields);
        $viewContentTemp = str_replace('<!--template_validate_start,template_validate_end-->', $viewFieldsTemplate, $viewContentTemp);


        $viewContentTemp = str_replace('Demo', $class, $viewContentTemp);
        $viewContentTemp = str_replace('demo', strtolower($module), $viewContentTemp);

        $viewDir = "$baseDir\/resource";
        $fileName = explode('/', $item);
        $fileName = array_pop($fileName);
        $moduleTemp = $class;
        $file = "$viewDir/$moduleTemp/$fileName";
        $path = dirname($file);
        if(!file_exists($path)){
            mkdir($path, '0777', true);
        }
        file_put_contents($file, $viewContentTemp);
    }
}





