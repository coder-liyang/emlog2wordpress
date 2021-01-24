<?php declare(strict_types=1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//连接emlog数据库
$GLOBALS['emlog'] = \core\Emlog::getInstance();
//连接wordpress数据库
$GLOBALS['wordpress'] = \core\Wordpress::getInstance();

$move = new \core\Move();
//迁移
$move->run();