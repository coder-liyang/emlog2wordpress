<?php


namespace core\wordpress;


use core\emlog\Sort;
use core\Model;
use core\WpDb;

/**
 * 分类和导航都保存在这
 * Class Terms
 * @package core\wordpress
 */
class Terms extends Model
{
    public $term_id;
    public $name;
    public $slug;
    public $term_group;

    public function push(\stdClass $sort)
    {
        //terms ID为1的分类会被认定为默认分类,如果不满足实际需求,后面需要手动修改
        $this->term_id = $sort->sid;
        $this->name = $sort->sortname;
        $this->slug = $sort->alias;
        $this->term_group = $sort->taxis;
        WpDb::db()->table('terms')->insert((array)$this);
        //terms_taxonomy
        $termsTaxonomy = new TermsTaxonomy();
        $termsTaxonomy->term_id = $this->term_id;
        $termsTaxonomy->taxonomy = 'category';
        $termsTaxonomy->description = $sort->description;
        $termsTaxonomy->parent = $sort->pid;
        $termsTaxonomy->count = 0; //分类下文章的数量,后期导入文章的时候再填这个值
        WpDb::db()->table('term_taxonomy')->insert((array)$termsTaxonomy);
    }
}