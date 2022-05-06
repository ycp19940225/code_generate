<?php

namespace App\code_template\swoft;

use App\code_template\swoft\tool\Func;

class SwoftInit
{

    /**
     * @var string|string[]
     */
    private $rootDir;
    /**
     * @var string
     */
    private $baseDir;
    /**
     * @var string
     */
    private $controlTemplate;
    /**
     * @var string
     */
    private $logicTemplate;
    /**
     * @var array
     */
    private $viewTemplate;
    /**
     * @var string
     */
    private $tempDir;

    public function __construct()
    {
        $this->rootDir = str_replace('\\','/',realpath(dirname(__FILE__).'/'));

        $this->baseDir = $this->rootDir .'/gen_code';
        $this->controlTemplate = $this->rootDir .'/template/controller/Controller.php';
        $this->logicTemplate = $this->rootDir .'/template/logic/Logic.php';
        $this->tempDir = $this->rootDir .'/temp/temp.php';

        $this->viewTemplate[] = $this->rootDir .'/template/view/index.php';
        $this->viewTemplate[] = $this->rootDir .'/template/view/form_add.php';
        $this->viewTemplate[] = $this->rootDir .'/template/view/form_edit.php';
    }
    public function run()
    {
        $input = $_POST;
//        if(empty($module)){
//            $input = json_decode(file_get_contents('php://input'), true);
//        }
        $module = $input['module'];
        if(empty($module)){
           error('必传参数module!');
        }
        $ConTroModule = $input['module_name'];
        $formType = $input['form_type'];
        $title = $input['title'];
        $fieldsArray = $input['formInfo'];

        $baseDir = $this->baseDir;
        $controlTemplate = $this->controlTemplate;
        $logicTemplate = $this->logicTemplate;
        $viewTemplate = $this->viewTemplate;
        $tempDir = $this->tempDir;


        copy($controlTemplate, $tempDir);

        $controllerContent = file_get_contents($tempDir);

        $moduleTemp = explode('_', $module);
        $class = [];
        foreach ($moduleTemp as $item) {
            $class[] = ucfirst($item);
        }
        $class = implode('', $class);
        $controllerContentTemp = templateReplace('Module', $class, $controllerContent);
        $controllerContentTemp = templateReplace('module', strtolower($module), $controllerContentTemp);
        if (!empty($ConTroModule)) {
            $ConTroModule = ucfirst($ConTroModule);
            $strTemp = "namespace App\Http\Controller\\$ConTroModule;";
            $controllerContentTemp = str_replace('namespace App\Http\Controller;', $strTemp, $controllerContentTemp);
        }
        if (!empty($title)) {
            $controllerContentTemp = templateReplace('title', $title, $controllerContentTemp);
        }
        // 替换title
        $tableFields = $editFields = $viewFields = $viewFormFields = $viewFormJsFields = $viewFormValidateFields = [];
        foreach ($fieldsArray as $item) {
            $str = $item['name'];
            $strName = $item['field'];
            // 列表字段
            $tableFields[] = str_repeat(' ', 4 * 4) . '$data[\'aaData\'][$k][] = $r[\'' . $str . '\'];';
            // 编辑字段
            $editFields[] = str_repeat(' ', 4 * 4) . '\'' . $str . '\' => $data[\'' . $str . '\'],';
            // 表格字段
            $viewFields[] = str_repeat(' ', 11 * 4) . '<th >' . $strName . '</th>';
            // 表单字段
            $viewFormFields[] = str_repeat(' ', 5 * 4) . '<div class="form-group">
                        <label>' . $strName . '</label>
                        <input type="text" id="' . $str . '" name="' . $str . '" class="form-control" placeholder="请输入' . $strName . '"/>
                    </div>';
            // 表单验证字段
            $viewFormJsFields[] = str_repeat(' ', 2 * 4) . '' . $str . ': {
            validators: {
                notEmpty: {
                    message: \'请选择' . $strName . '\'
                }
            }
        },';
            $viewFormEditFields[] = str_repeat(' ', 3 * 4) . 'preserve_form.find("input[name=\'' . $str . '\']").val(data.' . $str . ');';
            // 跳转表单
            $viewFormValidateFields[] = str_repeat(' ', 10 * 4) . '<div class="form-group col-lg-6">
                                            <label>' . $strName . '<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="' . $str . '" name="' . $str . '" class="form-control" placeholder="请输入' . $strName . '"/>
                                            </div>
                                        </div>';
        }
        $tableFieldsTemplate = implode(PHP_EOL, $tableFields);
        $controllerContentTemp = str_replace('// template_fields_start,template_fields_end', $tableFieldsTemplate, $controllerContentTemp);
        // 替换编辑添加字段
        $editFieldsTemplate = implode(PHP_EOL, $editFields);
        $controllerContentTemp = str_replace('// template_edit_fields_start,template_edit_fields_end', $editFieldsTemplate, $controllerContentTemp);
        $controllerContentTemp = str_replace('// template_add_fields_start,template_add_fields_end', $editFieldsTemplate, $controllerContentTemp);

        $handle1 = '                $action .= get_icon(\'edit\', [], base_url(\'admin/[% module %]/edit?id=\'. $r[\'id\']));';
        $handle1 = templateReplace('module', strtolower($module), $handle1);
        $handle2 = '                $action .= get_icon(\'edit\', [\'id\' => $r[\'id\'], \'operation\' => \'edit\']);';


        /***************************************************************controller***************************************************************/
        if ($formType == 1) {
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
        $data = [% Module %]Logic::getInfo([\'id\' => $id]);
        return responseJson(1, \'成功!\', $data);
    }';
            $editMethod = templateReplace('Module', $class, $editMethod);
            $editMethod = templateReplace('module', $class, $editMethod);
            $controllerContentTemp = str_replace('// template_edit_start,template_edit_end', $editMethod, $controllerContentTemp);
        } else {
            $controllerContentTemp = str_replace('// template_handle_start,template_handle_end', $handle1, $controllerContentTemp);
            $editMethod = '/**
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
            $data = [% Module %]Logic::getInfo([\'id\' => $id]);
            return view(\'[% module %]/form_edit\', $data);
        }else{
            return view(\'[% module %]/form_add\', $data);
        }
    }';
            $editMethod = templateReplace('Module', $class, $editMethod);
            $editMethod = templateReplace('module', $class, $editMethod);
            $controllerContentTemp = str_replace('// template_edit_start,template_edit_end', $editMethod, $controllerContentTemp);
        }

        $controllerDir = "$baseDir\app\Http\Controller\Admin";
        $file = "$controllerDir/$class" . "Controller.php";
        $path = dirname($file);
        if (!file_exists($path)) {
            mkdir($path, '0777', true);
        }
        file_put_contents($file, $controllerContentTemp);


        /***************************************************************model***************************************************************/
        copy($logicTemplate, $tempDir);
        $logicContent = file_get_contents($tempDir);

        $logicContentContentTemp = templateReplace('Module', $class, $logicContent);
        $logicContentContentTemp = templateReplace('module', strtolower($module), $logicContentContentTemp);

        $modelDir = "$baseDir\app\Model\Logic";
        $file = "$modelDir/$class" . "Logic.php";
        $path = dirname($file);
        if (!file_exists($path)) {
            mkdir($path, '0777', true);
        }
        file_put_contents($file, $logicContentContentTemp);


        /***************************************************************view***************************************************************/
        if ($formType == 1) {
            foreach ($viewTemplate as $item) {
                copy($item, $tempDir);
                $viewContentTemp = file_get_contents($tempDir);
                // 替换操作按钮
                $button = '<button type="button" class="btn btn-primary" id="preserve">
                                                    <i class="la la-plus-circle"></i> 添加
                                                </button>';
                $viewContentTemp = str_replace('<!--template_button_start,template_button_end-->', $button, $viewContentTemp);

                // 替换title
                $viewContentTemp = templateReplace('title', $title, $viewContentTemp);

                // 替换view表格字段
                $viewFieldsTemplate = implode(PHP_EOL, $viewFields);
                $viewContentTemp = str_replace('<!--template_view_fields_start,template_view_fields_end-->', $viewFieldsTemplate, $viewContentTemp);

                // 替换表单
                $formTemplate = getPackageTempLate('form');

                // 替换字段
                $viewFormFieldsTemplate = implode(PHP_EOL, $viewFormFields);
                $viewForm = str_replace('<!--template_form_fields_start,template_form_fields_end-->', $viewFormFieldsTemplate, $formTemplate);
                $viewContentTemp = str_replace('<!--template_form_start,template_form_end-->', $viewForm, $viewContentTemp);

                $formInfo = getPackageTempLate('formInfo');

                // 替换字段
                $viewFormJsTemplate = implode(PHP_EOL, $viewFormJsFields);
                $viewFormEditFields = implode(PHP_EOL, $viewFormEditFields);
                $viewJsForm = str_replace('<!--template_validate_start,template_validate_end-->', $viewFormJsTemplate, $formInfo);
                $viewJsForm = str_replace('<!--template_form_info_start,template_form_info_end-->', $viewFormEditFields, $viewJsForm);
                $viewContentTemp = str_replace('<!--template_form_js_start,template_form_js_end-->', $viewJsForm, $viewContentTemp);


                // 最后统一替换模块名称
                $viewContentTemp = templateReplace('Module', $class, $viewContentTemp);
                $viewContentTemp = templateReplace('module', strtolower($module), $viewContentTemp);


                $viewDir = "$baseDir\/resource";
                $fileName = explode('/', $item);
                $fileName = array_pop($fileName);
                $moduleTemp = $class;
                $file = "$viewDir/$moduleTemp/$fileName";
                $path = dirname($file);
                if (!file_exists($path)) {
                    mkdir($path, '0777', true);
                }
                file_put_contents($file, $viewContentTemp);
                break;
            }
        } else {
            foreach ($viewTemplate as $key => $item) {
                copy($item, $tempDir);
                $viewContentTemp = file_get_contents($tempDir);

                if ($key == 0) {
                    // 替换操作按钮
                    $button = '<a href="<?php echo base_url(\'admin/[% module %]/edit\');?>" type="button" class="btn btn-primary">
                                                    <i class="la la-plus-circle"></i> 添加
                                                </a>';
                    $viewContentTemp = str_replace('<!--template_button_start,template_button_end-->', $button, $viewContentTemp);
                }

                // 替换title
                $viewContentTemp = templateReplace('title', $title, $viewContentTemp);

                // 替换view表格字段
                $viewFieldsTemplate = implode(PHP_EOL, $viewFields);
                $viewContentTemp = str_replace('<!--template_view_fields_start,template_view_fields_end-->', $viewFieldsTemplate, $viewContentTemp);


                // 替换form字段
                $viewFieldsTemplate = implode(PHP_EOL, $viewFormValidateFields);
                $viewContentTemp = str_replace('<!--template_form_filed_start,template_form_filed_end-->', $viewFieldsTemplate, $viewContentTemp);

                // 替换form_js字段
                $viewFieldsTemplate = implode(PHP_EOL, $viewFormJsFields);
                $viewContentTemp = str_replace('<!--template_validate_start,template_validate_end-->', $viewFieldsTemplate, $viewContentTemp);


                $viewContentTemp = templateReplace('Module', $class, $viewContentTemp);
                $viewContentTemp = templateReplace('module', strtolower($module), $viewContentTemp);

                $viewDir = "$baseDir\/resource";
                $fileName = explode('/', $item);
                $fileName = array_pop($fileName);
                $moduleTemp = $class;
                $file = "$viewDir/$moduleTemp/$fileName";
                $path = dirname($file);
                if (!file_exists($path)) {
                    mkdir($path, '0777', true);
                }
                file_put_contents($file, $viewContentTemp);
            }
        }
        success();
    }
}





