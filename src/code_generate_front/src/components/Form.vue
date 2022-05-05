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
        <el-form-item label="sql">
            <el-input v-model="form.sql" type="textarea" @blur="createInputElement()"/>
        </el-form-item>
        <el-form-item
                v-for="(info, index) in form.formInfo"
                :key="index"
                :label="info.label"
                :prop="info.name"
                :rules="info.rules"
        >
            <el-form :inline="true" class="">
                <el-form-item label="字段名">
                    <el-input v-model="info.value" placeholder="请输入字段名" />
                </el-form-item>
                <el-form-item label="字段值">
                    <el-input v-model="info.value" placeholder="请输入字段值" />
                </el-form-item>
                <el-form-item label="字段类型">
                    <el-select v-model="info.value" placeholder="请选择字段类型">
                        <el-option label="文本" value="input" />
                        <el-option label="选择" value="select" />
                        <el-option label="日期" value="date" />
                        <el-option label="单选" value="radio" />
                        <el-option label="复选" value="checkbox" />
                        <el-option label="文本" value="textarea" />
                        <el-option label="文件" value="file" />
                        <el-option label="富文本" value="rich_textarea" />
                    </el-select>
                </el-form-item>
                <el-form-item label="是否为空">
                    <el-select v-model="info.value" placeholder="是否为空">
                        <el-option label="文本" value="input" />
                        <el-option label="文本" value="input" />
                    </el-select>
                </el-form-item>
            </el-form>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="onSubmit">Create</el-button>
        </el-form-item>
    </el-form>
</template>

<script lang="ts" setup>
    import { reactive } from 'vue'
    import { post } from '../http/http'
    import { ElNotification } from "element-plus";
    // do not use same name with ref
    const form = reactive({
        module: '',
        module_name: '',
        form_type: '',
        title: '',
        sql: '',
        formInfo: [{
            label:'test',
            prop:'ss',
            name:'test',
            rules:{
               
            },
        },
            {
                label:'test',
                prop:'ss',
                name:'test',
                rules:{

                },

            }
        ],
    })

    const onSubmit = () => {
        let url = '/api/test';
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
        data = data.toString().split(',');
        data = formatString(data);
        console.log(data)
        let inputElement = [];
        for(let i = 0; i < data.length; i++){
            inputElement.push([

            ])
        }
    }

    function formatString(str){
        for(let i = 0; i < str.length; i++){
            let temp = str[i];
            temp = temp.replace('/\n/g', '')
            str[i] = temp.trim()
        }
        return str;
    }
</script>
