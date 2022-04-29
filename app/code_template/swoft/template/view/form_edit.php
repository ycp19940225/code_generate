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
                                <h5 class="text-dark font-weight-bold my-1 mr-5">编辑[% title %]</h5>
                                <!--end::Page Title-->
                            </div>
                            <!--end::Page Heading-->
                        </div>
                        <!--end::Info-->

                        <div class="d-flex align-items-center">
                            <!--begin::Actions-->
                            <a href="<?php
                            if(!empty($url)){
                                echo $url;
                            }else{
                                echo base_url('admin/[% module %]/index');
                            }
                            ?>" class="btn btn-light font-weight-bolder btn-sm">
                                <i class="ki ki-bold-arrow-back icon-sm"></i> 返回</a>
                            <!--end::Actions-->
                        </div>
                    </div>
                </div>
                <!--end::Subheader-->
                <!--begin::Entry-->
                <div class="d-flex flex-column-fluid">
                    <!--begin::Container-->
                    <div class="container">
                        <form class="form" method="post" action="" id="preserve_form">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="card card-custom">
                                <div class="card-header">
                                    <h3 class="card-title">基本信息</h3>
                                </div>
                                <div class="card-body pb-0">
                                    <div class="form-group row">
<!--template_form_filed_start,template_form_filed_end-->
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-primary mr-2 submit">保存</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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

<?php $this->include('Common/js') ?>
<script>
    //添加验证器
    var validation = form_validation('preserve_form', {
<!--template_validate_start,template_validate_end-->
    });
    $(function () {
        // 表单赋值
        if(!empty("<?=$id?>")){
            $("select[name='status']").val(<?=$status?>);
            $("select").selectpicker('refresh')
        }
        var addStatus = 1;
        $(".submit").click(function () {
            if(addStatus){
                validation.validate().then(function (status) {
                    if (status === 'Valid') {
                        addStatus = false;
                        let url = "<?php echo base_url('admin/[% module %]/editDo'); ?>";
                        let params = serialize_object($("#preserve_form"));
                        $.axios.request('POST', url, params, function (response_data) {
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
        });
    });
</script>
</body>
<!--end::Body-->
</html>