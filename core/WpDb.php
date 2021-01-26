<?php

namespace core;


use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;

class WpDb
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
            'host'      => $_ENV['WORDPRESS_DB_HOSTNAME'],
            'database'  => $_ENV['WORDPRESS_DB_DATABASE'],
            'username'  => $_ENV['WORDPRESS_DB_USERNAME'],
            'password'  => $_ENV['WORDPRESS_DB_PASSWORD'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => $_ENV['WORDPRESS_DB_PREFIX'],
        ], 'wp');
        // $capsule->setAsGlobal();
        $this->db = $capsule->getConnection('wp');
    }

    static public function db(): ?Connection
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->db;
    }
}