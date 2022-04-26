<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Demo
 *
 */
class Demo extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Logic/Admin/Demo', 'Demo');
    }

    public function demoList()
    {
        $where = [
            'training_class_id' => $this->input->get('id')
        ];

        $data = $this->Demo->getList($where, '*');

        if (!empty($data['data'])) {
            foreach ($data['data'] as $key => $datum) {
                $data['aaData'][$key][] = $datum['id'];
// template_fields_start,template_fields_end
                $act = '<a href="javascript:void(0);" operation="edit" id="' . $datum['id'] . '" class="table-action-btn" data-placement="top" data-toggle="tooltip" data-original-title="编辑"><i class="md md-edit"></i></a> ';
                $act .= '<a href="javascript:void(0);" operation="delete" key="' . $this->input->get('id') . '" id="' . $datum['id'] . '" class="table-action-btn" data-placement="top" data-toggle="tooltip" data-original-title="删除"><i class="md md-close"></i></a>';
                $data['aaData'][$key][] = $act;

                $data['aaData'][$key][] = $act;
            }
        }

        exit(json_encode($data));
    }

    public function demoGetInfo()
    {
        $data = $this->input->post();
        $data = $this->Demo->getInfo($data['id']);
        $this->ajaxReturn(1, '操作成功', $data);
    }

    /**
     * 保存参考记录等
     */
    public function demoEditDo()
    {
        $data = $this->input->post();
        $res = $this->Demo->saveDo();
        if($res){
            $this->ajaxReturn(1, '操作成功', array(), site_url('Admins/Course/detail?id=' . $data['training_id'] . '&tab=' . $data['tab']));
        }else{
            $this->ajaxReturn(0, '操作失败', array(), site_url('Admins/Course/detail?id=' . $data['training_id'] . '&tab=' . $data['tab']));
        }
    }

    //删除资讯
    public function demoDelete()
    {
        $key = $this->input->post('key');
        $tab = $this->input->post('tab');
        if ($this->Demo->deleteData() == false) {
            $this->ajaxReturn(0, '删除失败');
        }
        $this->ajaxReturn(1, '删除成功', array(), site_url("Admins/Course/detail?id=$key&tab=$tab"));
    }
}
