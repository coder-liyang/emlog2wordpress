<?php declare(strict_types=1);

use core\WpDb;

error_reporting(E_ALL);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

printf("即将执行迁移,是否确认?[y/n]\n");
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'y'){
    echo "停止迁移!\n";
    exit;
}
fclose($handle);


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
echo "请手动将emlog下的/content/uploadfile文件夹复制到wordpress下的wp-content/uploads/emlog目录\n";
