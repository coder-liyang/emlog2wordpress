<?php


namespace core;

use Illuminate\Database\Connection;
use Illuminate\Database\Capsule\Manager as Capsule;

class ElDb
{
    /** @var Connection $db */
    public $db;

    /** @var null|ElDb $instance  */
    static private $instance = null;

    private function __construct()
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $_ENV['EMLOG_DB_HOSTNAME'],
            'database'  => $_ENV['EMLOG_DB_DATABASE'],
            'username'  => $_ENV['EMLOG_DB_USERNAME'],
            'password'  => $_ENV['EMLOG_DB_PASSWORD'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => $_ENV['EMLOG_DB_PREFIX'],
        ], 'el');
        // $capsule->setAsGlobal();
        $this->db = $capsule->getConnection('el');
    }

    static public function db(): Connection
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->db;
    }
}