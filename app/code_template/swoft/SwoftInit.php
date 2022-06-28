<?php

namespace App\code_template\swoft;

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
        $this->viewTemplate[] = $this->rootDir .'/template/view/show.php';
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
        $showPage = !empty($input['show_page']) ? $input['show_page'] : 0;
        $fieldsArray = $input['formInfo'];

        $baseDir = $this->baseDir;
        $controlTemplate = $this->controlTemplate;
        $logicTemplate = $this->logicTemplate;
        if ($formType == 1) {
            $viewTemplate[] = $this->viewTemplate[0];
            $viewTemplate[] = $this->viewTemplate[3];
        }else{
            $viewTemplate = $this->viewTemplate;
        }
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
        $tableFields = $editFields = $viewFields = $viewFormFields = $viewFormJsFields = $viewFormFields_2 = [];
        $extJsData = ['<script>'];
        foreach ($fieldsArray as $item) {
            $str = $item['name'];
            // 表单类型
            $type = $item['type'];
            $strName = trim($item['field']);
            if(in_array('form', $item['extraData'])){
                // 控制器编辑字段
                $editFields[] = templateReplace('field', $str, getPackageTempLate('editFields'));

                // 表单编辑赋值字段（弹窗1）
                $viewFormEditFields[] = templateReplace('field', $str, getPackageTempLate('viewFormEditFields'));
                if(in_array('required', $item['extraData'])){
                    $fieldRequired = '<span class="text-danger">*</span>';
                    // 表单验证字段js
                    if($type == 'select'){
                        $fieldTip = '选择';
                    }else if($type == 'image'){
                        $fieldTip = '上传';
                    }else{
                        $fieldTip = '填写';
                    }
                    $viewFormJsFields[] = templateReplace(['field', 'fieldName', 'fieldTip'], [$str, $strName, $fieldTip], getPackageTempLate('viewFormJsFields'));
                }else{
                    $fieldRequired = '';
                }
                // 表单字段（页面2）
                switch ($type){
                    case 'text':
                        $viewFormFieldsTemp = templateReplace(['field', 'fieldName', 'fieldRequired'], [$str, $strName, $fieldRequired], getPackageTempLate('viewFormFields_2'));
                        break;
                    default:
                        switch ($type){
                            case 'select':
                                if(!empty($item['selectExtraData']) && in_array('form_select_search', $item['selectExtraData'])){
                                    $form_select_search = 'data-live-search="true"';
                                }else{
                                    $form_select_search = '';
                                }
                                if(!empty($item['selectExtraData']) && in_array('form_select_multiple', $item['selectExtraData'])){
                                    $form_select_multiple = 'multiple';
                                }else{
                                    $form_select_multiple = '';
                                }
                                $viewFormFieldsTemp = templateReplace(['field', 'fieldName', 'fieldRequired', 'search', 'multiple'], [$str, $strName, $fieldRequired, $form_select_search, $form_select_multiple], getPackageTempLate('viewFormFields_2_' . $type));
                                break;
                            case 'radio':
                                if(!empty($item['type']['radioExtraData'])){
                                    $radioValue_1 = $item['radioValue_1'];
                                    $radioName_1 = $item['radioName_1'];
                                    $radioValue_2 = $item['radioValue_2'];
                                    $radioName_2 = $item['radioName_2'];
                                }else{
                                    $radioValue_1 = '1';
                                    $radioName_1 = '是';
                                    $radioValue_2 = '2';
                                    $radioName_2 = '否';
                                }
                                $viewFormFieldsTemp = templateReplace(['field', 'fieldName', 'fieldRequired', 'radioValue_1', 'radioName_1', 'radioValue_2', 'radioName_2'], [$str, $strName, $radioValue_1, $radioName_1, $radioValue_2, $radioName_2], getPackageTempLate('viewFormFields_2_' . $type));
                                break;
                            case 'date':
                                $extJsData[] = getPackageTempLate('formDateInit');
                                break;
                            case 'image':
                                $extJsData[] = getPackageTempLate('formImageInit');
                                $viewFormFieldsTemp = templateReplace(['field', 'fieldName', 'fieldRequired'], [$str, $strName, $fieldRequired], getPackageTempLate('viewFormFields_2_' . $type));
                                break;
                            default:
                                $viewFormFieldsTemp = templateReplace(['field', 'fieldName', 'fieldRequired'], [$str, $strName, $fieldRequired], getPackageTempLate('viewFormFields_2'));
                                break;
                        }
                        break;
                }
                // 表单字段（弹窗1）
                $viewFormFields[] = templateReplace('fieldCol', '', $viewFormFieldsTemp);
                $viewFormFields_2[] = templateReplace('fieldCol', 'col-lg-6', $viewFormFieldsTemp);
                // 表单编辑赋值（页面2）
                $viewFormEditValue[] = templateReplace(['field', 'fieldName', 'fieldRequired'], [$str, $strName, $fieldRequired], getPackageTempLate('viewFormEditValue'));
            }
            if(in_array('list', $item['extraData'])){
                // 列表字段
                $tableFields[] = templateReplace('field', $str, getPackageTempLate('tableFields'));
                // 表格字段
                $viewFields[] = templateReplace('fieldName', $strName, getPackageTempLate('viewFields'));
            }
            // 搜索
            if(in_array('search', $item['extraData'])){
                if($type == 'text' || $type == 'select'){
                    // 页面
                    $indexSearchFields[] = templateReplace(['field', 'fieldName'], [$str, $strName], getPackageTempLate('indexSearch_' . $type));
                    // 控制器搜索
                    $controllerSearchFields[] = templateReplace(['field', 'fieldName'], [$str, $strName], getPackageTempLate('controllerSearch'));

                }
            }
            if($str == 'indexid'){
                $sortAble = 'indexid';
            }
        }

        if(!empty($controllerSearchFields)){
            $controllerSearchFields = implode(PHP_EOL, $controllerSearchFields);
            $controllerContentTemp = templateReplace('extraListSearch', $controllerSearchFields, $controllerContentTemp);
        }else{
            $controllerContentTemp = templateReplace('extraListSearch', '', $controllerContentTemp);
        }
        if(!empty($sortAble)){
            $controllerContentTemp = templateReplace('sortFields', "'indexid desc'", $controllerContentTemp);
        }else{
            $controllerContentTemp = templateReplace('sortFields', "'id desc'", $controllerContentTemp);
        }
        $extJsData[] = '</script>';
        $tableFieldsTemplate = implode(PHP_EOL, $tableFields);
        $controllerContentTemp = str_replace('// template_fields_start,template_fields_end', $tableFieldsTemplate, $controllerContentTemp);
        // 替换编辑添加字段
        $editFieldsTemplate = implode(PHP_EOL, $editFields);
        $controllerContentTemp = str_replace('// template_edit_fields_start,template_edit_fields_end', $editFieldsTemplate, $controllerContentTemp);
        $controllerContentTemp = str_replace('// template_add_fields_start,template_add_fields_end', $editFieldsTemplate, $controllerContentTemp);

        $handle1 = getPackageTempLate('tableButton_edit_type_1');
        $handle1 = templateReplace('module', strtolower($module), $handle1);
        $handle2 = getPackageTempLate('tableButton_edit_type_2');

        $handle = [];
        // 详情页
        if($showPage == 1){
            $handle[] = templateReplace('module', strtolower($module), getPackageTempLate('tableButton_show'));
        }
        if ($formType == 1) {
            $handle[] = $handle2;
        }else{
            $handle[] = $handle1;
        }

        $handle[]= templateReplace('module', strtolower($module), getPackageTempLate('tableButton_delete'));

        /***************************************************************controller***************************************************************/
        $handle = implode(PHP_EOL, $handle);
        if ($formType == 1) {
            $controllerContentTemp = str_replace('// template_handle_start,template_handle_end', $handle, $controllerContentTemp);
            $editMethod = getPackageTempLate('editMethod');
            $editMethod = templateReplace('Module', $class, $editMethod);
            $editMethod = templateReplace('module', $class, $editMethod);
            $controllerContentTemp = str_replace('// template_edit_start,template_edit_end', $editMethod, $controllerContentTemp);
        } else {
            $controllerContentTemp = str_replace('// template_handle_start,template_handle_end', $handle, $controllerContentTemp);
            $editMethod = getPackageTempLate('editMethod2');
            $editMethod = templateReplace('Module', $class, $editMethod);
            $editMethod = templateReplace('module', $class, $editMethod);
            $controllerContentTemp = str_replace('// template_edit_start,template_edit_end', $editMethod, $controllerContentTemp);
        }

        // 增加show 方法
        if($showPage == 1){
            $temp = getPackageTempLate('controllerMethod_show');
            $temp = templateReplace('Module', $class, $temp);
            $extraMethod[] = $temp;
        }

        if(!empty($extraMethod)){
            $extraMethod = implode('', $extraMethod);
            $controllerContentTemp = templateReplace('extraMethod', $extraMethod, $controllerContentTemp);
        }else{
            $controllerContentTemp = templateReplace('extraMethod', '', $controllerContentTemp);
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
                $button = templateReplace(['module'], [$module], getPackageTempLate('indexButton_add'));
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
                if(is_array($viewFormEditFields)){
                    $viewFormEditFields = implode(PHP_EOL, $viewFormEditFields);
                }
                $viewJsForm = str_replace('<!--template_validate_start,template_validate_end-->', $viewFormJsTemplate, $formInfo);
                $viewJsForm = str_replace('<!--template_form_info_start,template_form_info_end-->', $viewFormEditFields, $viewJsForm);
                $viewContentTemp = templateReplace('template_form_js', $viewJsForm, $viewContentTemp);

                //图片日期等js 加载
                if(!empty($extJsData)){
                    $extJsDataTemplate = implode(PHP_EOL, $extJsData);
                    $extJsDataTemplate = templateReplace('ConTroModule', $ConTroModule, $extJsDataTemplate);
                    $viewContentTemp = templateReplace('extJsData', $extJsDataTemplate, $viewContentTemp);
                }
                // 搜索
                if(!empty($indexSearchFields)){
                    $indexSearchFieldsTemplate = implode(PHP_EOL, $indexSearchFields);
                    $viewContentTemp = templateReplace('indexSearchFieldsTemplate', $indexSearchFieldsTemplate, $viewContentTemp);
                }else{
                    $viewContentTemp = templateReplace('indexSearchFieldsTemplate', '', $viewContentTemp);
                }

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

                if ($key == 2) {
                    // 替换表单初始化
                    $viewFormEditValueTemplate = implode(PHP_EOL, $viewFormEditValue);
                    $viewContentTemp = templateReplace('viewFormEditValue', $viewFormEditValueTemplate, $viewContentTemp);;
                }

                // 替换title
                $viewContentTemp = templateReplace('title', $title, $viewContentTemp);

                // 替换view表格字段
                $viewFieldsTemplate = implode(PHP_EOL, $viewFields);
                $viewContentTemp = str_replace('<!--template_view_fields_start,template_view_fields_end-->', $viewFieldsTemplate, $viewContentTemp);


                // 替换form字段
                $viewFieldsTemplate = implode(PHP_EOL, $viewFormFields_2);
                $viewContentTemp = str_replace('<!--template_form_filed_start,template_form_filed_end-->', $viewFieldsTemplate, $viewContentTemp);

                // 替换form_js字段
                $viewFieldsTemplate = implode(PHP_EOL, $viewFormJsFields);
                $viewContentTemp = str_replace('<!--template_validate_start,template_validate_end-->', $viewFieldsTemplate, $viewContentTemp);

                // 搜索
                if(!empty($indexSearchFields)){
                    $indexSearchFieldsTemplate = implode(PHP_EOL, $indexSearchFields);
                    $viewContentTemp = templateReplace('indexSearchFieldsTemplate', $indexSearchFieldsTemplate, $viewContentTemp);
                }else{
                    $viewContentTemp = templateReplace('indexSearchFieldsTemplate', '', $viewContentTemp);
                }


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





