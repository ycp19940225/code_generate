<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <?php $this->include('Common/header') ?>
    <?php $this->include('Common/js') ?>
    <style>
        .gray_text{
            color: grey;
        }
        .goVerify{
            cursor: pointer;
        }
        .table_search{
            margin-top: 12px;
        }
        .card.card-custom > .card-body {
            padding: 1rem 2.25rem 2rem;
        }
        .la-link{
            color: #337ab7;
        }

        .search_item_box{
            display: flex;
            flex-direction: row;
        }
        .search_item_title{
            display: flex;
            align-items: center;
            /*justify-content: flex-end;*/
            font-weight: 700;
            /*padding-right: 14px;*/
            min-width: 60px;
            text-align: right;
        }
        .search_item_box .search_item{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
        }
        .item{
            display: flex;
            align-items: center;
        }
        .select_more{
            justify-content: flex-start !important;
        }
        .select_more .item:not(first-child){
            margin-left: 15px;
        }
        .select_more .item:first-child{
            margin-left: 0;
        }
        .card-toolbar{
            display: flex;
            justify-content: flex-end;
        }
        #material_list img{
            max-height: 150px;
        }

        .course_name_box, .course_name_box_empty{
            margin-top: 0.5rem;
        }
    </style>
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
                                <h5 class="text-dark font-weight-bold my-1 mr-5"><?=$data['name']?></h5>
                            </div>
                            <!--end::Page Heading-->
                        </div>
                        <!--end::Info-->
                        <div class="d-flex align-items-center">
                            <!--begin::Actions-->
                            <a href="<?php echo base_url('admin/[% module %]/index'); ?>" class="btn btn-light font-weight-bolder btn-sm">
                                <i class="ki ki-bold-arrow-back icon-sm"></i> ??????</a>
                            <!--end::Actions-->
                        </div>
                    </div>
                </div>
                <!--end::Subheader-->
                <!--begin::Entry-->
                <div class="d-flex flex-column-fluid">
                    <!--begin::Container-->
                    <div class="container" >
                        <div class="card card-custom mb-10">
                            <div class="card-body" style="min-height: 450px">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item " role="presentation">
                                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">????????????</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link <?php if(!checkPermission(['audio_program_view'])):?>active<?php endif;?>" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">??????</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade  show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <div class="mt-10">
                                            <div class="mb-7">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-2 col-xl-2">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-12 my-2 my-md-0">
                                                                <div class="">
                                                                    <input type="text" class="form-control dt_search" placeholder="??????"   name="s_keywords">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-xl-2">
                                                        <select class="form-control dt_search selectpicker"  name="tag_id" data-show-tick="true" title="????????????">
                                                            <option value="-1" selected>????????????</option>
                                                            <?php if(!empty($tagsList)): ?>
                                                                <?php foreach ($tagsList as $key => $tag): ?>
                                                                    <option value="<?=$tag['id']?>"><?=$tag['name']?></option>
                                                                <?php endforeach;?>
                                                            <?php endif;?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-xl-2">
                                                        <select class="form-control dt_search selectpicker"  name="status" data-show-tick="true" title="????????????">
                                                            <option value="-1" selected>????????????</option>
                                                            <option value="0">??????</option>
                                                            <option value="1">??????</option>
                                                            <option value="2">??????</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3 col-xl-3">
                                                        <a href="javascript:void(0)" class="btn btn-light-primary font-weight-bold" id="search_audio_program_relation"><i class="fa fa-search"></i>??????</a>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="btn-group float-right">
                                                            <?php if(checkPermission(['audio_program_create'])):?>
                                                                <button type="button" class="btn btn-primary" id="preserve_audio_program_relation">
                                                                    <i class="la la-plus-circle"></i>??????
                                                                </button>
                                                            <?php endif;?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-head-custom table-vertical-center" id="list_preserve_audio_program_relation">
                                                    <thead>
                                                    <tr class="text-left">
                                                        <th >??????????????????</th>
                                                        <th >??????</th>
                                                        <th style="width: 10%;">????????????ID</th>
                                                        <th style="width: 10%;">????????????</th>
                                                        <th style="width: 10%;">??????</th>
                                                        <th style="width: 10%;">??????</th>
                                                        <th style="width: 10%;">????????????</th>
                                                        <th style="width: 10%;">??????</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade <?php if(!checkPermission(['audio_program_view'])):?>show active<?php endif;?>" id="home" role="tabpanel" aria-labelledby="home-tab">
                                        <div class="mt-10">
                                            <div class="mb-7">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-2 col-xl-2">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-12 my-2 my-md-0">
                                                                <div class="">
                                                                    <input type="text" class="form-control dt_search" placeholder="??????"  name="s_keywords">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-xl-2">
                                                        <select class="form-control dt_search selectpicker"  name="status" data-show-tick="true" title="????????????">
                                                            <option value="-1" selected>????????????</option>
                                                            <option value="1">??????</option>
                                                            <option value="2">??????</option>
                                                            <option value="0">??????</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3 col-xl-3">
                                                        <a href="javascript:void(0)" class="btn btn-light-primary font-weight-bold" id="search_button"><i class="fa fa-search"></i>??????</a>
                                                    </div>
                                                    <div class="col-lg-5">
                                                        <div class="btn-group float-right">
                                                                <button type="button" class="btn btn-primary" id="preserve">
                                                                    <i class="la la-plus-circle"></i>??????
                                                                </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-head-custom table-vertical-center" id="list">
                                                    <thead>
                                                    <tr class="text-left">
                                                        <th style="width: 5%;">ID</th>
                                                        <th >??????</th>
                                                        <th style="width: 7%;">??????</th>
                                                        <th style="width: 5%;">??????</th>
                                                        <th style="width: 12%;">??????</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<script>

</body>
</html>


