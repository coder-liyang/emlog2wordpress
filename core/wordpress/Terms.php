<?php


namespace core\wordpress;


use core\emlog\Sort;
use core\Model;
use core\Wordpress;

class Terms extends Model
{
    public $term_id;
    public $name;
    public $slug;
    public $term_group;

    public function tableName(): string
    {
        return $_ENV['WORDPRESS_DB_PREFIX'] . 'terms';
    }

    public function push(Sort $sort)
    {
        $this->term_id = $sort->sid;
        $this->name = $sort->sortname;
        $this->slug = $sort->alias;
        $this->term_group = $sort->taxis;
        Wordpress::getInstance()->db->exec("insert into " . $this->tableName());
    }
}