<!--标签-->
<div class="row">
    <div class="col-sm-12 table-container">
        <div class="pull-right">
            <a href="javascript:void(0);" id="add_demo"
               class="btn btn-default waves-effect waves-light">
                <i class="fa fa-plus"></i>&nbsp;新增标签
            </a>
        </div>
        <table class="table table-hover table-actions-bar nowrap table-nowrap"
               id="demo_lists">
            <thead>
            <tr>
                <th sort="false" width="5%">ID</th>
<!--template_view_fields_start,template_view_fields_end-->
                <th sort="false" width="80">操作</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<!-- modal 新增标签 start -->
<div class="modal fade modal_small" id="add_demo_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> 新增标签</h4>
            </div>
            <form class="form-horizontal form-row-seperated" method="post" id="add_demo_form"
                  action="<?php echo site_url('Admins/Course/demoEditDo'); ?>" enctype="multipart/form-data">
                <div class="modal-body form">
<!--template_form_filed_start,template_form_filed_end-->
                </div>
                <div class="modal-footer">
                    <input type="hidden" class="form-control" name="id" value=""/>
                    <input type="hidden" class="form-control" name="tab" value="7"/>
                    <input type="hidden" name="training_id" value="<?php echo $id;?>">
                    <button type="submit" class="ladda-button btn btn-primary">
                        <i class="fa fa-save"></i>
                        <span class="create_btn_name">保存</span>
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                        关闭
                    </button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- modal 新增标签 end -->
<div id="del_demo_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="del_content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="custom-width-modalLabel">删除</h4>
            </div>
            <form class="form-horizontal form-row-seperated" method="post" id="del_demo_form"
                  action="<?php echo site_url('/Admins/Course/demoDelete'); ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <p del="msg">确定删除？</p>
                </div>
                <input type="hidden" class="form-control" name="id" value=""/>
                <input type="hidden" class="form-control" name="key" value=""/>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light submit_btn">
                        确定
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        取消
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        var demo_modal = $('#add_demo_modal');
        var demo_form = $('#add_demo_form');
        $("#add_demo").click(function () {
            resetForm(demo_form);
            $(demo_form).find('[name="id"]').val('');
            demo_modal.modal();
        })

        //编辑
        $('#demo_lists').on('click', '[operation="edit"]', function () {

            $(demo_modal).find('[class="modal-title"]').html('编辑');
            resetForm(demo_modal);
            var url = '<?php echo site_url('Admins/Course/demoGetInfo');?>';
            var params = {
                id: $(this).attr('id'),
            };
            new AjaxPostRequest(url, params, function (data) {
                $(demo_form).find('[name="id"]').val(data.data.id);
<!--template_form_edit_start,template_form_edit_end-->
                demo_modal.modal();
            });
        });

        $.e_validate.init(demo_form, {
            returnFun: function (data) {
                demo_modal.modal('hide');
                showMessage(data);
                setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }
        });

        //删除
        var del_demo_modal = $('#del_demo_modal');
        var del_demo_form = $('#del_demo_form');

        $('#demo_lists').on('click', '[operation="delete"]', function () {
            var delete_id = $(this).attr('id');
            var key = $(this).attr('key');
            $(del_demo_form).find("[name='id']").val(delete_id);
            $(del_demo_form).find("[name='key']").val(key);
            del_demo_modal.modal();
        });

        $.e_validate.init(del_demo_form, {
            returnFun: function (data) {
                del_demo_modal.modal('hide');
                showMessage(data);
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        });
    })
</script>
