<?php
require("Article.php");
require("Comment.php");

class AllTests extends PHPUnit_Framework_TestCase {
    public $test_db = "tests/test_db.sqlite";

    public function testSetup() {
        $db_init   = "db/init.sql";
        $test_data = "tests/test_data.sql";

        exec("sqlite3 $this->test_db -init $db_init < $test_data", $ouput, $rcode);
        $this->assertEquals(0, $rcode);
        if ( $rcode != 0 ) var_dump($output);
    }

    public function testGetArticles() {
        $article_ids = Article::get_articles($this->test_db);
        $this->assertEquals([3,2,1], $article_ids, "\$canonicalize = true", $delta = 0.0, $maxDepth = 10, $canonicalize = true);

        return $article_ids;
    }

    /**
     * @depends testGetArticles
     */
    public function testArticleLoad(array $article_ids) {
        foreach ( $article_ids as $id ) {
            $article = Article::load($id, $this->test_db);

            $this->assertInstanceOf("Article", $article);
            $this->assertInstanceOf("Post", $article);
            $this->assertGreaterThan(0, $article->get_id());
            $this->assertGreaterThan(0, $article->get_author_id());
            $this->assertEquals(0, $article->get_parent_post_id());
            $this->assertEquals(0, $article->get_root_post_id());
            $this->assertStringMatchesFormat("Blog Author%d", $article->get_author());
            $this->assertStringMatchesFormat("%a", $article->get_content());
        }
    }

    public function testGetComments() {
        $article = Article::load(1, $this->test_db);
        $comment_ids = $article->get_comments();
        $this->assertEquals([4,5], $comment_ids, "\$canonicalize = true", $delta = 0.0, $maxDepth = 10, $canonicalize = true);

        return $comment_ids;
    }

    /**
     * @depends testGetComments
     */
    public function testCommentLoad(array $comment_ids) {
        foreach ( $comment_ids as $id ) {
            $comment = Comment::load($id, $this->test_db);

            $this->assertInstanceOf("Comment", $comment);
            $this->assertInstanceOf("Post", $comment);
            $this->assertGreaterThan(0, $comment->get_id());
            $this->assertGreaterThan(0, $comment->get_author_id());
            $this->assertGreaterThan(0, $comment->get_parent_id());
            $this->assertGreaterThan(0, $comment->get_article_id());
            $this->assertStringMatchesFormat("Commenter %d", $comment->get_author());
            $this->assertStringMatchesFormat("%a", $comment->get_content());
        }
    }

    public function testGetReplies() {
        $comment = Comment::load(5, $this->test_db);

        $ids = $comment->get_replies();
        $this->assertEquals([6,7], $ids, "\$canonicalize = true", $delta = 0.0, $maxDepth = 10, $canonicalize = true);

        $reply = Comment::load($ids[0], $this->test_db);
        $this->assertInstanceOf("Comment", $reply);
        $this->assertInstanceOf("Post", $reply);
        $this->assertEquals(6, $reply->get_id());
        $this->assertEquals(5, $reply->get_author_id());
        $this->assertEquals(5, $reply->get_parent_id());
        $this->assertEquals(1, $reply->get_article_id());
        $this->assertEquals("Commenter 3", $reply->get_author());
        $this->assertEquals("Positive retort", $reply->get_content());
    
        $ids = $reply->get_replies();
        $this->assertEquals([8], $ids);
        
        $reply = Comment::load($ids[0], $this->test_db);
        $this->assertInstanceOf("Comment", $reply);
        $this->assertInstanceOf("Post", $reply);
        $this->assertEquals(8, $reply->get_id());
        $this->assertEquals(4, $reply->get_author_id());
        $this->assertEquals(6, $reply->get_parent_id());
        $this->assertEquals(1, $reply->get_article_id());
        $this->assertEquals("Commenter 2", $reply->get_author());
        $this->assertEquals("Trollish reply", $reply->get_content());

        $ids = $reply->get_replies();
        $this->assertEquals([], $ids);
    }

    public function testCreateArticle() {
        $author_id = 2;
        $content = "Testing a new article";

        $new = Article::create($author_id, $content, $this->test_db);
        $this->assertEquals($author_id, $new->get_author_id());
        $this->assertEquals($content, $new->get_content());
        $this->assertNull($new->get_id());
        $this->assertNull($new->get_author());
        $this->assertNull($new->get_parent_post_id());
        $this->assertNull($new->get_root_post_id());

        $new->save();
        $this->assertEquals($author_id, $new->get_author_id());
        $this->assertEquals($content, $new->get_content());
        $this->assertEquals(11, $new->get_id());
        $this->assertEquals("Blog Author2", $new->get_author());
        $this->assertEquals(0, $new->get_parent_post_id());
        $this->assertEquals(0, $new->get_root_post_id());

        return $new->get_id();
    }

    /**
     * @depends testCreateArticle
     */
    public function testCreateComment($article_id) {
        $author_id = 3;
        $content = "Testing a new comment";

        $new = Comment::create($author_id, $content, $article_id, $article_id, $this->test_db);
        $this->assertEquals($author_id, $new->get_author_id());
        $this->assertEquals($content, $new->get_content());
        $this->assertNull($new->get_id());
        $this->assertNull($new->get_author());
        $this->assertEquals($article_id, $new->get_parent_id());
        $this->assertEquals($article_id, $new->get_article_id());

        $new->save();
        $this->assertEquals($author_id, $new->get_author_id());
        $this->assertEquals($content, $new->get_content());
        $this->assertEquals(12, $new->get_id());
        $this->assertEquals("Commenter 1", $new->get_author());
        $this->assertEquals($article_id, $new->get_parent_id());
        $this->assertEquals($article_id, $new->get_article_id());

        $parent_id = $new->get_id();
        $author_id = 4;
        $content = "Testing a new reply";

        $new = Comment::create($author_id, $content, $parent_id, $article_id, $this->test_db);
        $new->save();
        $this->assertEquals($author_id, $new->get_author_id());
        $this->assertEquals($content, $new->get_content());
        $this->assertEquals(13, $new->get_id());
        $this->assertEquals("Commenter 2", $new->get_author());
        $this->assertEquals($parent_id, $new->get_parent_id());
        $this->assertEquals($article_id, $new->get_article_id());

        //Test implicit article_id
        $new = Comment::create($author_id, $content, $article_id);
        $this->assertEquals($author_id, $new->get_author_id());
        $this->assertEquals($content, $new->get_content());
        $this->assertNull($new->get_id());
        $this->assertNull($new->get_author());
        $this->assertEquals($article_id, $new->get_parent_id());
        $this->assertEquals($article_id, $new->get_article_id());
    }

    public function testTearDown() {
        exec("rm $this->test_db", $output, $rcode);
        $this->assertEquals(0, $rcode);
        $this->assertEquals(100, 100); //Test 100 :)
    }
}
?>
