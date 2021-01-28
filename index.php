<?php declare(strict_types=1);

use core\WpDb;

error_reporting(E_ALL);

require 'vendor/autoload.php';

$envFilePath = isset($argv[1])?$argv[1]:__DIR__;
$dotenv = Dotenv\Dotenv::createImmutable($envFilePath);
$dotenv->load();

printf("即将执行迁移,是否确认?[y/n]\n");
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'y'){
    echo "停止迁移!\n";exit;
}
printf("内容中上传的附件,原前缀为:%s\n" . rtrim($_ENV['EMLOG_REPLACE'], '/') . '/content/uploadfile');
printf("将要替换为:%s\n" . rtrim($_ENV['WORDPRESS_REPLACE']) . '/wp-content/uploads/emlog');
printf("您需要在迁移结束后,手动将emlog下的`/content/uploadfile`文件夹复制到wordpress下的`wp-content/uploads`目录,并改名为emlog\n");
printf("例:xxx/wp-content/uploads/emlog/{年月日的文件夹}\n");
printf("Linux命令:`cp -r {emlog目录}/content/uploadfile {emlog目录}/wp-content/uploads/emlog`\n");
printf("确定吗?[y/n]");
$line = fgets($handle);
if(trim($line) != 'y'){
    echo "停止迁移!\n";exit;
}
printf("此操作会清空不会修改任何emlog数据,但会清空WordPress下的分类/文章/评论,无法恢复!");
printf("确定吗?[yes/no]");
$line = fgets($handle);
if(trim($line) != 'yes'){
    echo "停止迁移,敏感操作,如确需执行请输入'yes'!\n";exit;
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
