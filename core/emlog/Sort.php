<?php


namespace core\emlog;


use core\getAllYield;
use core\Model;

class Sort extends Model
{
    // protected $connection = 'default';

    use getAllYield;

    public $sid;
    public $sortname;
    public $alias;
    public $taxis;
    public $pid;
    public $description;
    public $template;

}