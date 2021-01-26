<?php declare(strict_types=1);

namespace core;

use core\emlog\Link;
use core\emlog\Sort;
use core\wordpress\Links;
use core\wordpress\Posts;
use core\wordpress\Terms;
use core\wordpress\TermsRelationships;
use Illuminate\Database\Capsule\Manager as Capsule;

class Move
{
    public function run(): bool
    {
        //分类
        $this->sort();
        //内容
        $this->blog();
        //评论
        return true;
    }


    /**
     * 分类迁移
     */
    public function sort()
    {
        WpDb::db()->table('terms')->truncate();
        WpDb::db()->table('term_taxonomy')->truncate();
        WpDb::db()->table('term_relationships')->truncate();
        $wordpressTerms = new Terms();
        foreach(ElDb::db()->table('sort')->get() as $row) {
            $wordpressTerms->push($row);
        }
    }

    public function navi()
    {
        //创建一个名为from_emlog的菜单组
        $term_id = WpDb::db()->table('terms')->insertGetId([
            'name' => 'from_emlog',
            'slug' => 'from_emlog',
            'term_group' => 0,
        ]);
        $term_taxonomy_id = WpDb::db()->table('term_taxonomy')->insertGetId([
            'term_id' => $term_id,
            'taxonomy' => 'nav_menu',
            'parent' => 0,
            'count' => 0
        ]);
        //插入导航
        foreach (ElDb::db()->table('navi')->get() as $row) {
            $termsRelationships = new TermsRelationships();
            $termsRelationships->object_id = $row['id'];
            $termsRelationships->term_taxonomy_id = $term_taxonomy_id;
            $termsRelationships->term_order = 0;
            WpDb::db()->table('term_relationships')->insert((array)$termsRelationships);
        }
    }

    public function blog()
    {
        WpDb::db()->table('posts')->truncate();
        foreach (ElDb::db()->table('blog')->get() as $row) {
            $posts = new Posts();
            $posts->ID = $row->gid;
            $posts->post_author = $row->author;
            $posts->post_date = date('Y-m-d H:i:s', $row->date);
            $posts->post_date_gmt = date('Y-m-d H:i:s', $row->date - 3600 * 8);
            $posts->post_content = str_replace(
                $_ENV['EMLOG_REPLACE'],
                $_ENV['WORDPRESS_REPLACE'],
                $row->content
            );//转换一下内容中的附件地址
            $posts->post_title = $row->title;
            $posts->post_excerpt = str_replace(
                $_ENV['EMLOG_REPLACE'],
                $_ENV['WORDPRESS_REPLACE'],
                $row->excerpt
            );//转换一下内容中的附件地址
            $posts->post_status = $row->hide == 'n' ? 'publish' : 'draft';
            $posts->post_name = urlencode($row->title);
            $posts->comment_status = 'open';
            $posts->ping_status = 'open';
            $posts->to_ping = '';
            $posts->pinged = '';
            $posts->post_modified = $posts->post_date;
            $posts->post_modified_gmt = $posts->post_date_gmt;
            $posts->post_content_filtered = '';
            $posts->post_parent = 0;
            $posts->guid = '';
            $posts->menu_order = 0;
            $posts->post_type = 'post'; //TODO
            $posts->post_mime_type = '';
            $posts->comment_count = 0;
            WpDb::db()->table('posts')->insert((array)$posts);
            //
            $termsRelationships = new TermsRelationships();
            $termsRelationships->object_id = $row->gid;
            // $termsRelationships->term_taxonomy_id = $row->sortid;
            $termsRelationships->term_taxonomy_id = (int)WpDb::db()->table('term_taxonomy')->where(['term_id' => $row->sortid])->first()->term_taxonomy_id;
            $termsRelationships->term_order = 0;
            WpDb::db()->table('term_relationships')->insert((array)$termsRelationships);
        }
    }
}