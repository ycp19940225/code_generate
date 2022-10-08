<?php declare(strict_types=1);

namespace App\Model\Common;

use Swoft\Db\DB;
use Swoft\Stdlib\Helper\ArrayHelper;

/**
 * Class CommonLogic
 * @package App\Model\Common
 */
class CommonLogic
{
//      $data = [
//            'object_type' => $object_type,
//            ['whereIn', 'alias', $alias_list]
//            ['id', '>', $last_reply_info['id']],
//            ['name','like',"%{$keywords}%"],
//            [
//                'orWhere',
//                'mobile',
//                'like',
//                "%{$s_keywords}%"
//            ],
//            [
//                'whereRaw',
//                "(name like '%{$s_keywords}%' or mobile like '%{$s_keywords}%')",
//            ]
//            [
//                'whereBetween',
//                'createtime',
//                [
//                    $start,
//                    $end
//                ]
//            ]
//        ];

//    $join[0]['join'] = [
//        "demotest",
//        "demotest.key",
//        '=',
//        "demo.key"
//    ];
    /**
     * 获取列表数据
     * @param string $tableName
     * @param array $data
     * @param bool $paging false（不分页，相当于获取所有） | true（分页）
     * @param string $orderBy
     * @param array $groupBy
     * @param array $join
     * @param string $rows 'id,content,type'
     * @param bool $count 分页时是否求count
     * @param string $pool
     * @param string $db
     * @return array|array[]|object[]|string[]
     */
    public static function pagination(string $tableName, array $data = [], bool $paging = true, string $orderBy = '', array $groupBy = [], array $join = [], string $rows = '*', bool $count = true, string $pool = '', string $db = '')
    {
        $start = (int)get_val_by_key($data, 'start', 0);
        $length = (int)get_val_by_key($data, 'length', 50);

        unset($data['start'], $data['length']);

        $object = self::getQueryDb($tableName, $pool, $db)->where($data);

        if ($join) {
            foreach ($join as $key => $val) {
                foreach ($val as $k => $v) {
                    $object->$k($v[0], $v[1], $v[2], $v[3]);
                }
            }
        }

        if (!empty($groupBy)) {
            $object->groupBy($groupBy);
        }
        if (!empty($orderBy)) {
            $object->orderByRaw($orderBy);
        }

        if (!empty($paging) && !empty($count)) {
            $total = $object->count();
            $return_data['data'] = $object->selectRaw($rows)->offset($start)->limit($length)->get()->toArray();
            $return_data['recordsPage'] = ceil($total / $length);
            $return_data['recordsLength'] = $length;
            $return_data["recordsTotal"] = $total;
            $return_data["recordsFiltered"] = $total;
        } elseif (!empty($paging) && empty($count)) {
            $return_data = $object->selectRaw($rows)->offset($start)->limit($length)->get()->toArray();
        } else {
            $return_data = $object->selectRaw($rows)->get()->toArray();
        }

        return ArrayHelper::toArray($return_data);
    }

    /**
     * 新增
     * @param string $tableName
     * @param array $data
     * @param string $pool
     * @param string $db
     * @return string
     */
    public static function increase(string $tableName, array $data, string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);
        return $object->insertGetId($data);
    }

    /**
     * 批量新增
     * @param string $tableName
     * @param array $data
     * @param string $pool
     * @param string $db
     * @return bool
     */
    public static function increaseBatch(string $tableName, array $data, string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);
        return $object->insert($data);
    }

    /**
     * 编辑
     * @param string $tableName
     * @param array $where
     * @param array $data
     * @param string $pool
     * @param string $db
     * @return int
     */
    public static function preserve(string $tableName, array $where, array $data, string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);

        return $object->where($where)->update($data);
    }

    /**
     * 编辑批量
     * @param string $tableName
     * @param array $where
     * @param array $data
     * @param string $primary 主键字段
     * @param string $pool
     * @param string $db
     * @return int
     */
    public static function preserveBatch(string $tableName, array $where, array $data, string $primary = 'id', string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);
        if (!empty($where)) {
            $object->where($where);
        }

        return $object->batchUpdateByIds($data, $primary);
    }

    /**
     * 删除
     * @param string $tableName
     * @param array $data
     * @param string $pool
     * @param string $db
     * @return int
     */
    public static function decrease(string $tableName, array $data, string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);

        return $object->where($data)->delete();
    }

    /**
     * 获取详情
     * @param string $tableName
     * @param array $data
     * @param string $orderBy
     * @param string $rows
     * @param string $pool
     * @param string $db
     * @return array
     */
    public static function get_single(string $tableName, array $data, string $orderBy = '', string $rows = '*', string $pool = '', string $db = ''): array
    {
        $object = self::getQueryDb($tableName, $pool, $db);

        $build = $object->where($data);
        if (!empty($orderBy)) {
            $build->orderByRaw($orderBy);
        }

        return $build->selectRaw($rows)->firstArray();
    }

    /**
     * 查询数量
     * @param string $tableName
     * @param array $data
     * @param string $pool
     * @param string $db
     * @return int
     */
    public static function getCount(string $tableName, array $data, string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);

        return $object->where($data)->count();
    }

    /**
     * 求合
     * @param string $tableName
     * @param array $data
     * @param string $column
     * @param string $pool
     * @param string $db
     * @return float|int
     */
    public static function getSum(string $tableName, array $data, string $column, string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);

        return $object->where($data)->sum($column);
    }

    /**
     * 求某字段最大值
     * @param string $tableName
     * @param array $data
     * @param string $column
     * @param string $pool
     * @param string $db
     * @return float|int
     */
    public static function getMax(string $tableName, array $data, string $column, string $pool = '', string $db = '')
    {
        $object = self::getQueryDb($tableName, $pool, $db);

        $max = $object->where($data)->max($column);

        return empty($max) ? 10 : $max + 10;
    }

    /**
     * 选择数据库连接池和数据库
     * @param string $tableName
     * @param string $pool
     * @param string $db
     * @return \Swoft\Db\Query\Builder
     */
    public static function getQueryDb(string $tableName, string $pool = '', string $db = '')
    {
        if (!empty($pool) && !empty($db)) {
            $object = DB::query($pool)->db($db)->from($tableName);
        } elseif (!empty($pool) && empty($db)) {
            $object = DB::query($pool)->from($tableName);
        } elseif (empty($pool) && !empty($db)) {
            $object = DB::table($tableName)->db($db);
        } else {
            $object = DB::table($tableName);
        }
        return $object;
    }
}
