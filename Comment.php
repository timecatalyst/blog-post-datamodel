<?php
require_once("Post.php");

/**
 * Object class for creating and loading Comment blog posts.
 *
 * $comment = Comment::create($author_id, $content, $parent_id, $article_id);
 * $comment->save();
 *
 * $comment = Comment::load($id);
 * echo $comment->get_content();
 *
 * $replies = $comment->get_replies();
 */
class Comment extends Post {
    const SQL_GET_REPLIES = "SELECT id FROM posts WHERE parent_post_id = ?";

    /**
     * Semantic accessor to Post::parent_post_id.
     * @return unsigned int
     */
    public function get_parent_id()  { return $this->parent_post_id; }

    /** 
     * Semantic accessor to Post::root_post_id.
     * @return unsigned int
     */
    public function get_article_id() { return $this->root_post_id; } 
    
    /**
     * Static method for creating a new Comment object.
     *
     * @param unsigned int $author_id  The Comment's author.
     * @param string $content          The Comment's content.
     * @param unsigned int $parent_id  The parent Article or Comment of the current Comment.
     * @param unsigned int $article_id The root Article the reply or comment belongs to.
     * @param string $db_file          Optional. Specify a custom sqlite3 db file.
     * @return Comment
     */
    public static function create($author_id, $content, $parent_id, $article_id = null, $db_file = null) {
        return new self([
            'author_id'      => $author_id,
            'content'        => $content,
            'parent_post_id' => $parent_id,
            'root_post_id'   => $article_id,
            'db_file'        => $db_file
        ]);
    }

    /** 
     * Static method for loading an existing Comment from backstore.
     * 
     * @param unsigned int $id The Comment backstore ID.
     * @param string $db_file  Optional. Specify a custom sqlite3 db file.
     * @return Comment
     */
    public static function load($id, $db_file = null) { 
        return new self([ 'id' => $id, 'db_file' => $db_file ]);
    }

    /**
     * Returns Comment IDs of all first-child replies to the current Comment.
     * @return array
     */
    public function get_replies() {
        $dbh = $this->_db_connect();
        $stmt = $dbh->prepare(self::SQL_GET_REPLIES);
        $stmt->bindValue(1, $this->id);
        $returned_set = $stmt->execute();

        $id_list = array();
        while ( $row = $returned_set->fetchArray() )
            array_push($id_list, $row['id']);

       return $id_list; 
    }
}
?>
