<?php declare(strict_types=1);
namespace core\emlog;

use core\Emlog;
use core\getAllYield;
use core\Model;
use Generator;

class Link extends Model
{
    use getAllYield;

    /** @var int $id */
    public $id;
    /** @var string $sitename */
    public $sitename;
    /** @var string $siteurl */
    public $siteurl;
    /** @var string $description */
    public $description;
    /** @var string $hide 'n','y' */
    public $hide;
    /** @var int $taxis 排序,小的靠前 */
    public $taxis;

    public function tableName(): string
    {
        return $_ENV['EMLOG_DB_PREFIX'] . 'link';
    }


}