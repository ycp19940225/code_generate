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
            <el-input v-model="form.sql" type="textarea" />
        </el-form-item>

        <el-form-item
                v-for="(info, index) in form.formInfo"
                :key="index"
                :label="info.label"
                :prop="info.name"
                :rules="info.rules"
        >
            <el-input v-model="info.value" />
        </el-form-item>

        <el-form-item>
            <el-button type="primary" @click="onSubmit">Create</el-button>
            <el-button>Cancel</el-button>
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
               
            }
        }],
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
</script>
