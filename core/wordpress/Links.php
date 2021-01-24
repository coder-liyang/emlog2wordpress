<?php declare(strict_types=1);
namespace core\wordpress;

use core\emlog\Link;
use core\Model;
use core\Wordpress;
use PDO;

class Links extends Model
{
    /** @var PDO $db */
    public $db;

    /** @var $link_id int */
    public $link_id;
    /** @var $link_url string */
    public $link_url;
    /** @var $link_name string */
    public $link_name;
    /** @var $link_image string */
    public $link_image;
    /** @var $link_target string */
    public $link_target;
    /** @var $link_description string */
    public $link_description;
    /** @var $link_visible string */
    public $link_visible;
    /** @var $link_owner string */
    public $link_owner;
    /** @var $link_rating int */
    public $link_rating;
    /** @var $link_updated string */
    public $link_updated;
    /** @var $link_rel string */
    public $link_rel;
    /** @var $link_notes string */
    public $link_notes;
    /** @var $link_rss string */
    public $link_rss;

    public function tableName(): string
    {
        return $_ENV['WORDPRESS_DB_PREFIX'] . 'link';
    }

    public function push(Link $link)
    {
        $this->link_id = $link->id;
        $this->link_name = $link->sitename;
        $this->link_url = $link->siteurl;
        $this->link_description = $link->description;
        var_dump($link);exit;
    }
}