<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <?php $this->include('Common/header') ?>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body"
      class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
        <!--begin::Aside-->
        <?php $this->include('Common/sidebar') ?>
        <!--end::Aside-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
            <!--begin::Header-->
            <?php $this->include('Common/topbar') ?>
            <!--end::Header-->
            <!--begin::Content-->
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <!--begin::Subheader-->
                <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
                    <div
                        class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                        <!--begin::Info-->
                        <div class="d-flex align-items-center flex-wrap mr-1">
                            <!--begin::Page Heading-->
                            <div class="d-flex align-items-baseline flex-wrap mr-5">
                                <!--begin::Page Title-->
                                <h5 class="text-dark font-weight-bold my-1 mr-5">[% title %]</h5>
                                <!--end::Page Title-->
                            </div>
                            <!--end::Page Heading-->
                        </div>
                        <!--end::Info-->
                    </div>
                </div>
                <!--end::Subheader-->
                <!--begin::Entry-->
                <div class="d-flex flex-column-fluid">
                    <!--begin::Container-->
                    <div class="container">
                        <div class="card card-custom">
                            <div class="card-body" style="min-height: 540px;">
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        [% indexSearchFieldsTemplate %]
                                        <div class="col-lg-2 col-xl-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-12 my-2 my-md-0">
                                                    <div class="">
                                                        <input type="text" class="form-control dt_search" placeholder="名称" id="s_keywords" name="s_keywords">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-xl-2">
                                            <select class="form-control dt_search selectpicker" id="status" name="status" data-show-tick="true" title="所有状态">
                                                <option value="-1" selected>所有状态</option>
                                                <option value="1">显示</option>
                                                <option value="2">测试</option>
                                                <option value="0">隐藏</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-xl-3">
                                            <a href="javascript:void(0)" class="btn btn-light-primary font-weight-bold" id="search_button"><i class="fa fa-search"></i>搜索</a>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="btn-group float-right">
                                                 <!--template_button_start,template_button_end-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive" style="min-height: 600px">
                                    <table class="table table-head-custom table-vertical-center" id="list_[% module %]">
                                        <thead>
                                        <tr class="text-left">
                                            <th style="width: 5%;">ID</th>
<!--template_view_fields_start,template_view_fields_end-->
                                            <th style="width: 5%;">操作</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Entry-->
            </div>
            <!--end::Content-->
            <!--begin::Footer-->
            <?php $this->include('Common/footer') ?>
            <!--end::Footer-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::Main-->
<!--template_form_start,template_form_end-->
<?php $this->include('Common/js') ?>
<script>

    $(".datepicker").datepicker({
        toggleActive: true,
        autoclose: true,
        format: "yyyy-mm-dd",
        language: "zh-CN"
    });

    //获取列表操作
    let list_[% module %] = $('#list_[% module %]');
    getList(list_[% module %], {
        ajax: "<?php echo base_url('admin/[% module %]/list');?>",
    });

    //点击搜索操作
    $('#search_button').on('click', function () {
        list_[% module %].dataTable().fnDraw();
    });

    //表单页面添加
    [% template_form_js %]
    //删除
    $(document).on('click', '[data-operation="delete"]', function () {
        var name = $(this).data('name');
        let id = $(this).data('id');
        tip('<?php echo base_url('admin/[% module %]/delete');?>', {
            'id': id,
        }, '确认要删除吗？', name, function (response_data) {
            list_[% module %].dataTable().fnDraw();
            showMessage(response_data, false);
        })
    })
</script>
[% extJsData %]
</body>
<!--end::Body-->
</html>
