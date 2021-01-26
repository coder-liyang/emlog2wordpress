<?php


namespace core;


use Generator;

trait getAllYield
{
    /**
     * 用迭代器获取所有数据
     * @return Generator
     */
    public function getAllYield(): Generator
    {
        $res = ElDb::db()->query(sprintf('select * from %s', $this->tableName()));
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
            yield $this->bind(new self(), $row);
        }
    }
}