<?php declare(strict_types=1);

namespace core;

use core\emlog\Link;
use core\emlog\Sort;
use core\wordpress\Links;
use core\wordpress\Terms;

class Move
{
    public function run()
    {
        // $this->link();
        $this->sort();
    }

    /**
     * 迁移友情链接
     */
    public function link()
    {
        $emlogLink = new Link();
        $wordpressLinks = new Links();
        $wordpressLinks->truncate(Wordpress::getInstance(), $wordpressLinks->tableName());
        /** @var Link $row */
        foreach ($emlogLink->getAllYield() as $row) {
            $wordpressLinks->push($row);
        }
    }

    /**
     * 分类迁移
     */
    public function sort()
    {
        $emlogSort = new Sort();
        $wordpressTerms = new Terms();
        $wordpressTerms->truncate(Wordpress::getInstance(), $wordpressTerms->tableName());
        /** @var Sort $row */
        foreach ($emlogSort->getAllYield() as $row) {
            $wordpressTerms->push($row);
        }
    }
}