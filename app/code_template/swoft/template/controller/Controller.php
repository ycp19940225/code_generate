<?php declare(strict_types=1);

namespace App\Http\Controller;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use App\Model\Logic\[% Module %]Logic;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use App\Http\Middleware\AuthMiddleware;
use Throwable;

/**
 * [% title %]
 * Class [% Module %]Controller
 * @Controller("/admin/[% module %]")
 * @Middleware(AuthMiddleware::class)
 */
class [% Module %]Controller
{

    /**
     * 列表页
     * @RequestMapping()
     * @return Response
     * @throws Throwable
     */
    public function index()
    {
        $data = [];
        return view('[% Module %]/index', $data);
    }


    /**
     * 列表页数据
     * @RequestMapping()
     * @param Request $request
     * @return array
     */
    public function list(Request $request)
    {
        $request_data = $request->get();

        $start = (int)get_val_by_key($request_data, 'start', 0);
        $length = (int)get_val_by_key($request_data, 'length', 50);

        $where = [
            'start' => $start,
            'length' => $length,
        ];

        // 状态
        $status = get_val_by_key($request_data, 'status', -1);
        if ($status != -1) {
            $where['status'] = $status;
        } else {
            $where[] = ['status', '!=', 9];
        }

        // 昵称
        $s_keywords = get_val_by_key($request_data, 's_keywords', '');
        if (!empty($s_keywords)) {
            $where[] = [
                'whereRaw',
                "(name like '%{$s_keywords}%')",
            ];
        }
[% extraListSearch %]


        $data = [% Module %]Logic::getList($where, true, [% sortFields %]);

        if (!empty($data['data'])) {
            foreach ($data['data'] as $k => $r) {
                $data['aaData'][$k][] = $r['id'];
// template_fields_start,template_fields_end
                $data['aaData'][$k][] = getStatusTitle($r['status']);
                $action = '';
// template_handle_start,template_handle_end
                $data['aaData'][$k][] = $action;
            }
        }
        return $data;
    }

// template_edit_start,template_edit_end

    /**
     * 编辑新增
     * @RequestMapping()
     * @param Request $request
     * @param Response $response
     * @return array
     */
    public function editDo(Request $request, Response $response)
    {
        $data = $request->post();

        if (!empty($data['id'])) { //编辑
            $updateData = [
// template_edit_fields_start,template_edit_fields_end
                'status' => $data['status'],
            ];
            $res[] = [% Module %]Logic::edit(['id' => $data['id']], $updateData);
        } else {
            $insertData = [
// template_add_fields_start,template_add_fields_end
                'status' => $data['status'],
            ];
            $res[] = $id = [% Module %]Logic::add($insertData);
        }
        if (!in_array(false, $res)) {
            return responseJson(1, '成功!', ['url' => base_url('admin/[% module %]/index')]);
        } else {
            return responseJson(0, '失败');
        }
    }


    /**
     * 删除
     * @RequestMapping()
     * @param Request $request
     * @param Response $response
     * @return array
     */
    public function delete(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $request_data = $request->post();
            $id = (int)get_val_by_key($request_data, 'id');
//            $query = [% Module %]Logic::delete(['id' => $id,]);
            $query = [% Module %]Logic::edit(['id' => $id], ['status' => 9]);
            if ($query) {
                return responseJson(1, '删除成功!');
            } else {
                return responseJson(0, '删除失败!');
            }
        }
        return responseJson(0, '删除失败!');
    }

    /**
     * 获取信息
     * @RequestMapping()
     */
    public function getMaxIndexId()
    {
        $indexid = [% Module %]Logic::max([['status', '!=', '9'] ], 'indexid');
        return responseJson(1, '操作成功', $indexid);
    }

[% extraMethod %]
}
