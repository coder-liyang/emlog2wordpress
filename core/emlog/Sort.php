<?php


namespace core\emlog;


use core\getAllYield;
use core\Model;

class Sort extends Model
{
    use getAllYield;

    public $sid;
    public $sortname;
    public $alias;
    public $taxis;
    public $pid;
    public $description;
    public $template;

    /**
     * @return string
     */
    public function tableName(): string
    {
        return $_ENV['EMLOG_DB_PREFIX'] . 'sort';
    }

}