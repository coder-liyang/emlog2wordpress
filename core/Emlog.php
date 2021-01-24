<?php


namespace core;

use PDO;

class Emlog
{
    /** @var PDO $db */
    public $db;

    /** @var null|PDO */
    static private $instance = null;

    private function __construct()
    {
        $this->db = new PDO(
            sprintf('mysql:dbname=%s;host=%s;charset=UTF8', $_ENV['EMLOG_DB_DATABASE'], $_ENV['EMLOG_DB_HOSTNAME']),
            $_ENV['EMLOG_DB_USERNAME'],
            $_ENV['EMLOG_DB_PASSWORD']
        );
    }

    static public function getInstance(): ?Emlog
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}