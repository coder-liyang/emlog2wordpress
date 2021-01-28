<?php declare(strict_types=1);

namespace core;

use core\wordpress\Comments;
use core\wordpress\Posts;
use core\wordpress\Terms;
use core\wordpress\TermsRelationships;
use core\wordpress\TermsTaxonomy;
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
        $this->comment();
        //修正一些数据
        $this->fix();
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
        foreach(ElDb::db()->table('sort')->get() as $sort) {
            $terms = new Terms();
            //terms ID为1的分类会被认定为默认分类,如果不满足实际需求,后面需要手动修改
            $terms->term_id = $sort->sid;
            $terms->name = $sort->sortname;
            $terms->slug = $sort->alias;
            $terms->term_group = $sort->taxis;
            WpDb::db()->table('terms')->insert((array)$terms);
            //terms_taxonomy
            $termsTaxonomy = new TermsTaxonomy();
            $termsTaxonomy->term_id = $terms->term_id;
            $termsTaxonomy->taxonomy = 'category';
            $termsTaxonomy->description = $sort->description;
            $termsTaxonomy->parent = $sort->pid;
            $termsTaxonomy->count = 0; //分类下文章的数量,后期导入文章的时候再填这个值
            WpDb::db()->table('term_taxonomy')->insert((array)$termsTaxonomy);
            printf("分类:%s,迁移成功\n", $sort->sortname);
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
        $post_type = [
            'blog' => 'post',
            'page' => 'page',
        ];
        WpDb::db()->table('posts')->truncate();
        foreach (ElDb::db()->table('blog')->get() as $row) {
            $emlogPrefix = rtrim($_ENV['EMLOG_REPLACE'], '/') . '/content/uploadfile';
            $wordpressPrefix = rtrim($_ENV['WORDPRESS_REPLACE']) . '/wp-content/uploads/emlog';
            $posts = new Posts();
            $posts->ID = $row->gid;
            $posts->post_author = $row->author;
            $posts->post_date = date('Y-m-d H:i:s', $row->date);
            $posts->post_date_gmt = date('Y-m-d H:i:s', $row->date - 3600 * 8);
            $posts->post_content = str_replace(
                $emlogPrefix,
                $wordpressPrefix,
                $row->content
            );//转换一下内容中的附件地址
            $posts->post_title = $row->title;
            $posts->post_excerpt = str_replace(
                $emlogPrefix,
                $wordpressPrefix,
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
            $posts->post_type = $post_type[$row->type];
            $posts->post_mime_type = '';
            $posts->comment_count = 0;
            WpDb::db()->table('posts')->insert((array)$posts);
            //
            $termsRelationships = new TermsRelationships();
            $termsRelationships->object_id = $row->gid;
            // $termsRelationships->term_taxonomy_id = $row->sortid;
            $termsRelationships->term_taxonomy_id = ($temp = WpDb::db()->table('term_taxonomy')->where(['term_id' => $row->sortid])->first())?$temp->term_taxonomy_id:0;
            $termsRelationships->term_order = 0;
            WpDb::db()->table('term_relationships')->insert((array)$termsRelationships);

            printf("文章:%s,迁移成功\n", $row->title);
        }

    }

    public function comment()
    {
        WpDb::db()->table('comments')->truncate();
        foreach (ElDb::db()->table('comment')->get() as $row) {
            $comments = new Comments();
            $comments->comment_ID = $row->cid;
            $comments->comment_post_ID = $row->gid;
            $comments->comment_author = $row->poster;
            $comments->comment_author_email = $row->mail;
            $comments->comment_author_url = $row->url;
            $comments->comment_author_IP = $row->ip;
            $comments->comment_date = date('Y-m-d H:i:s', $row->date);
            $comments->comment_date_gmt = date('Y-m-d H:i:s', $row->date - 3600 * 8);
            $comments->comment_content = $row->comment;
            $comments->comment_karma = 0;
            $comments->comment_approved = $row->hide == 'n' ? 1 : 0;
            $comments->comment_agent = '';
            $comments->comment_type = 'comment';
            $comments->comment_parent = $row->pid;
            $comments->user_id = 0;
            WpDb::db()->table('comments')->insert((array)$comments);
            printf("评论:%s,迁移成功\n", $row->comment);

        }
    }

    public function fix()
    {
        printf("由于WordPress将ID为1的分类作为默认分类,因此这里将1号分类给重置成为默认分类\n");
        //查看1分类目前的内容
        $id1 = WpDb::db()->table('terms')->where(['term_id' => 1])->first();
        $taxonomy1 = WpDb::db()->table('term_taxonomy')->where(['term_id' => 1])->first();
        //修改1的分类为'未分类', parent=0
        WpDb::db()->table('terms')->where(['term_id' => 1])->update(['name' => '未分类', 'slug' => 'uncategorized']);
        WpDb::db()->table('term_taxonomy')->where(['term_id' => 1])->update(['parent' => 0]);
        //将1分类的内容复制一份插入,得到新ID
        $id1Arr = (array)$id1;
        unset($id1Arr['term_id']);
        $newId = WpDb::db()->table('terms')->insertGetId($id1Arr);
        $taxonomy1Arr = (array)$taxonomy1;
        unset($taxonomy1Arr['term_taxonomy_id']);
        $taxonomy1Arr['term_id'] = $newId;
        $new_term_taxonomy_id = WpDb::db()->table('term_taxonomy')->insertGetId($taxonomy1Arr);
        //将分类中父ID为1的改为新ID
        WpDb::db()->table('term_taxonomy')->where(['parent' => 1])->update(['parent' => $new_term_taxonomy_id]);
        //将文章中分类ID为1的改为新ID
        WpDb::db()->table('term_relationships')->where(['term_taxonomy_id' => $taxonomy1->term_taxonomy_id])->update(['term_taxonomy_id' => $new_term_taxonomy_id]);
        printf("数据修正完成\n");
    }
}