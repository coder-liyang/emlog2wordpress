<?php

namespace core;


use PDO;

class Wordpress
{
    /** @var PDO $db */
    public $db;

    /** @var null|PDO */
    static private $instance = null;

    private function __construct()
    {
        $this->db = new PDO(
            sprintf('mysql:dbname=%s;host=%s;charset=UTF8', $_ENV['WORDPRESS_DB_DATABASE'], $_ENV['WORDPRESS_DB_HOSTNAME']),
            $_ENV['WORDPRESS_DB_USERNAME'],
            $_ENV['WORDPRESS_DB_PASSWORD']
        );
    }

    static public function getInstance(): ?WORDPRESS
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}