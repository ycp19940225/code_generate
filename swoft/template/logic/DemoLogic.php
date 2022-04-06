<?php declare(strict_types=1);

namespace App\Model\Logic;

use App\Model\Common\CommonLogic;

class DemoLogic extends CommonLogic
{
    const POOL = 'db.pool';

    const DB = '';

    const TABLE = 'demo';

    /**
     * 新增
     * @param array $data
     * @return string
     */
    public static function add(array $data)
    {
        $data['created_time'] = date('Y-m-d H:i:s');
        $data['updated_time'] = date('Y-m-d H:i:s');
        return parent::increase(self::TABLE, $data, self::POOL, self::DB);
    }

    /**
     * 批量新增
     * @param array $data
     * @return string
     */
    public static function addBatch(array $data)
    {
        foreach ($data as $key => $item) {
            $data[$key]['created_time'] = date('Y-m-d H:i:s');
            $data[$key]['updated_time'] = date('Y-m-d H:i:s');
        }

        return parent::increaseBatch(self::TABLE, $data, self::POOL, self::DB);
    }

    /**
     * 编辑
     * @param array $where
     * @param array $data
     * @return int
     */
    public static function edit(array $where, array $data)
    {
        $data['updated_time'] = date('Y-m-d H:i:s');
        return parent::preserve(self::TABLE, $where, $data, self::POOL, self::DB);
    }

    /**
     * 批量修改
     * @param array $where
     * @param array $data
     * @param string $primary
     * @return int
     */
    public static function editBatch(array $where, array $data, string $primary = 'id')
    {
        foreach ($data as $key => $item) {
            $data[$key]['updated_time'] = date('Y-m-d H:i:s');
        }

        return parent::preserveBatch(self::TABLE, $where, $data, $primary, self::POOL, self::DB);
    }

    /**
     * 获取列表
     * @param array $data
     * @param bool $paging
     * @param string $orderBy
     * @param array $groupBy
     * @param array $join
     * @param string $rows
     * @param bool $count
     * @return array
     */
    public static function getList(array $data = [], bool $paging = true, string $orderBy = 'id desc', array $groupBy = [], array $join = [], string $rows = '*', bool $count = true): array
    {
        return parent::pagination(self::TABLE, $data, $paging, $orderBy, $groupBy, $join, $rows, $count, self::POOL, self::DB);
    }

    /**
     * 获取详情
     * @param array $data
     * @param string $order_by
     * @param string $rows
     * @return array
     */
    public static function getInfo(array $data, string $order_by = 'id asc', string $rows = '*'): array
    {
        return parent::get_single(self::TABLE, $data, $order_by, $rows, self::POOL, self::DB);
    }

    /**
     * 删除
     * @param array $data
     * @return int
     */
    public static function delete(array $data)
    {
        return parent::decrease(self::TABLE, $data, self::POOL, self::DB);
    }

    /**
     * 获取最大值
     * @param array $data
     * @param string $col
     * @return float|int
     */
    public static function max(array $data, string $col = 'indexid')
    {
        return parent::getMax(self::TABLE, $data, $col, self::POOL, self::DB);
    }

    /**
     * 获取最大值
     * @param array $data
     * @return int
     */
    public static function count(array $data)
    {
        return parent::getCount(self::TABLE, $data, self::POOL, self::DB);
    }

    /**
     * 求和
     * @param array $data
     * @param string $col
     * @return float|int
     */
    public static function sum(array $data, string $col = 'indexid')
    {
        return parent::getSum(self::TABLE, $data, $col, self::POOL, self::DB);
    }
}
