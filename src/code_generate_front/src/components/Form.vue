<template>
    <el-form :model="form" label-width="120px">
        <el-form-item label="模块名">
            <el-input v-model="form.module" />
        </el-form-item>
        <el-form-item label="控制器模块">
            <el-input v-model="form.module_name" />
        </el-form-item>
        <el-form-item label="表单类型">
            <el-select v-model="form.form_type" placeholder="请选择表单类型">
                <el-option label="弹窗" value="1" />
                <el-option label="页面" value="2" />
            </el-select>
        </el-form-item>
        <el-form-item label="页面标题">
            <el-input v-model="form.title" />
        </el-form-item>
        <el-form-item label="关联页(展示页)">
            <el-radio-group v-model="form.show_page">
                <el-radio label="0">无</el-radio>
                <el-radio label="1">有</el-radio>
            </el-radio-group>
        </el-form-item>
        <el-form-item label="sql">
            <el-input v-model="form.sql" type="textarea" @blur="createInputElement()"/>
        </el-form-item>
        <el-form-item
                v-for="(item, index) in form.formInfo"
                :key="index"
                :label="item.label"
                :prop="item.name"
                :rules="item.rules"
        >
            <el-form :inline="true" class="">
                <el-form-item label="字段名">
                    <el-input v-model="item.field" placeholder="请输入字段名" />
                </el-form-item>
                <el-form-item label="字段值">
                    <el-input v-model="item.name" placeholder="请输入字段值" />
                </el-form-item>
                <el-form-item label="字段类型">
                    <el-select v-model="item.type" placeholder="请选择字段类型">
                        <el-option label="文本" value="text" />
                        <el-option label="数字" value="number" />
                        <el-option label="选择" value="select" />
                        <el-option label="日期" value="date" />
                        <el-option label="单选" value="radio" />
                        <el-option label="复选" value="checkbox" />
                        <el-option label="多行文本" value="textarea" />
                        <el-option label="图片" value="image" />
                        <el-option label="文件" value="file" />
                        <el-option label="富文本" value="rich_textarea" />
                    </el-select>
                </el-form-item>
                <el-form-item label="下拉框选项" v-if="item.type == 'select'">
                    <el-checkbox-group v-model="item.selectExtraData">
                        <el-checkbox label="form_select_search">多选</el-checkbox>
                        <el-checkbox label="form_select_multiple">搜索</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item label="可选项">
                    <el-checkbox-group v-model="item.extraData">
                        <el-checkbox label="form" >表单</el-checkbox>
                        <el-checkbox label="required">必填</el-checkbox>
                        <el-checkbox label="list">列表</el-checkbox>
                        <el-checkbox label="search">搜索</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item label="选项" v-if="form.form_type == 2">
                    <el-checkbox-group v-model="item.extraData">
                        <el-checkbox label="form" >表单</el-checkbox>
                        <el-checkbox label="required">必填</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item label="单选选项" v-if="item.type == 'radio'">
                    <el-row :gutter="20">
                        <el-col :span="5">
                            <el-input v-model="item.radioName_1" placeholder="请输入选项1名" />
                        </el-col>
                        <el-col :span="5">
                            <el-input v-model="item.radioValue_1" placeholder="请输入选项1值" />
                        </el-col>
                        <el-col :span="5">
                            <el-input v-model="item.radioName_2" placeholder="请输入选项2名" />
                        </el-col>
                        <el-col :span="5">
                            <el-input v-model="item.radioValue_2" placeholder="请输入选项2值" />
                        </el-col>
                    </el-row>
                </el-form-item>
            </el-form>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="onSubmit">Create</el-button>
        </el-form-item>
    </el-form>
</template>

<script lang="ts" setup>
    import { reactive, watch } from 'vue'
    import { post } from '../tools/http/http'
    import { empty } from '../tools/tools'
    import { ElNotification } from "element-plus";
    // do not use same name with ref
    const form = reactive({
        module: '',
        module_name: 'admin',
        form_type: '',
        title: '',
        sql: '',
        show_page: '0',
        formInfo: [],
    })
    //还有第二种写法
    watch(() => form.formInfo, (newValue, oldValue) => {
        // 因为watch被观察的对象只能是getter/effect函数、ref、热active对象或者这些类型是数组
        // 所以需要将state.count变成getter函数
        console.log(newValue, oldValue)
    })
    const onSubmit = () => {
        let url = '/api/run';
        post(url, form).then(function (res){
            if(res.status == 1){
                ElNotification({
                    title: '成功',
                    message: '生成成功',
                    type: 'success',
                })
            }
        })
    }

    function createInputElement(){
        let data = form.sql;
        data = formatString(data);
        let inputElement = [];
        for(let i = 0; i < data.length; i++){
            let item = data[i];
            let itemArray = item.split(' ');
            let name = itemArray[0];
            let field = /COMMENT\s?(.*?)$/.exec(item);
            field = !empty(field) ? field[1] : '';
            let type = itemArray[1];
            type = getInputType(type)
            inputElement.push({
                label:`字段${i+1}`,
                prop:'',
                rules:{

                },
                name : name,
                field : field,
                type : type,
                extraData : ['form', 'required', 'list'],
            })
            form.formInfo = inputElement
        }
    }

    function getInputType(type){
        let res = '';
        if(/int/.test(type)){
            res = 'number';
        }
        if(/varchar/.test(type)){
            res = 'text';
        }
        return res;
    }
    function formatString(str){
        str = str.split(',');
        let res = [];
        for(let i = 0; i < str.length; i++){
            let temp = str[i];
            temp = temp.replace('/\n/g', '')
            temp = temp.trim()
            temp = temp.replace(/`/g, '');
            temp = temp.replace(/'/g, '');
            if(!empty(temp)){
                res.push(temp)
            }
        }
        return res;
    }
</script>
