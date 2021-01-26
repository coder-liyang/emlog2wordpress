<?php declare(strict_types=1);

use core\WpDb;

error_reporting(E_ALL);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// //连接emlog数据库
\core\ElDb::db();
// //连接wordpress数据库
\core\WpDb::db();


$move = new \core\Move();
//迁移
WpDb::db()->beginTransaction();
try {
    $move->run();
} catch (\Exception $exception) {
    WpDb::db()->rollBack();
    $error = $exception->getMessage();
    echo "迁移失败:{$error}\n";
    exit();
}
WpDb::db()->commit();
echo "迁移完成\n";
echo "请手动将emlog下的/content/uploadfile文件夹复制到wordpress下的wp-content/uploads/emlog目录";
