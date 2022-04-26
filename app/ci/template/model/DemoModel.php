<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 培训班-标签
 * Class AdminSiteModel
 */
class DemoModel extends MY_Model
{
    protected $table_name = 'demo';
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 保存培训班-班主任信息
	 * @return bool|int
	 */
	public function saveDo()
	{
		$data = $this->input->post();

		$updateData = array(
// template_edit_fields_start,template_edit_fields_end
		);
		$now = date('Y-m-d H:i:s');
		if ($data['id']) {
			//修改
			$this->db->where('id', $data['id']);
            $updateData['update_time'] = $now;
			return $this->update($this->table_name, $updateData);
		} else {
			//新增
            $updateData['create_time'] = $now;
            $updateData['update_time'] = $now;
			return $this->insert($this->table_name, $updateData);
		}
	}


    /**
     * 获取培训班-Tag列表
     * @param string $where
     * @param string $row
     * @return array
     */
    public function getList($where = [], $page = true, $row = '*')
    {
        $where['status'] = 1;
        $this->db->where($where);
        $this->db->select($row);
        $this->db->order_by('index_id desc');

        if($page){
            $data = $this->pagination($this->table_name);
            return $data;
        }else{
            return $this->getAll($this->table_name);
        }
    }

	/**
	 * 获取培训班-Tag信息
	 * @param string $id
	 * @return array
	 */
	public function getInfo($id = '')
	{

		$this->db->where('id', $id);

		$data = $this->getOne($this->table_name);

		return $data;
	}

    public function deleteData()
    {
        $data = $this->input->post();
        if(empty($data['id'])){
            return false;
        }
        $where = [
            'id' => $data['id'],
        ];
        $this->db->where($where);

//        $query = $this->delete($this->table_name);
        $updateData = [
            'status' => 9
        ];
        $query = $this->update($this->table_name, $updateData);

        return $query;
	}
}