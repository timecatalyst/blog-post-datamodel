<?php 
require_once("Post.php");

/**
 * Object class for creating and loading Article blog posts.
 *
 * $article = Article::create($author_id, $content);
 * $article->save();
 *
 * $article = Article::load($id);
 * echo $article->content();
 *
 * $comments = $article->get_comments();
 */
class Article extends Post {
    const SQL_GET_ALL_ARTICLES = 'SELECT id FROM posts WHERE parent_post_id = 0 AND root_post_id = 0';
    const SQL_GET_COMMENTS     = 'SELECT id FROM posts WHERE parent_post_id = ?';

    /**
     * Static method for creating a new Article object.
     *
     * @param unsigned int $author_id The Article's author.
     * @param string $content         The Article content.
     * @param string $db_file         Optional. Specify a custom sqlite3 db file.
     * @return Article
     */
    public static function create($author_id, $content, $db_file = null) {
        return new self([
            'author_id' => $author_id,
            'content'   => $content,
            'db_file'   => $db_file
        ]);
    }

    /** 
     * Static method for loading an existing Article from backstore.
     * 
     * @param unsigned int $id The Article backstore ID.
     * @param string $db_file  Optional. Specify a custom sqlite3 db file.
     * @return Article
     */
    public static function load($id, $db_file = null) { 
        return new self([ 'id' => $id, 'db_file' => $db_file ]);
    }
    
    /**
     * Static method. Retrieves a list of all article IDs in the backstore.
     *
     * @param string $db_file Optional. Specify a custom sqlite3 db file.
     * @return array
     */
    public static function get_articles($db_file = null) {
        $dbh = self::_db_connect($db_file);
        $stmt = $dbh->prepare(self::SQL_GET_ALL_ARTICLES);
        $returned_set = $stmt->execute();
        
        $id_list = array();
        while ( $row = $returned_set->fetchArray() ) 
            array_push($id_list, $row['id']);

        return $id_list;
    }
    
    /**
     * Returns Comment IDs of all first-child comments of the current Article.  
     * Does not retrieve replies to comments (See Comment::get_replies()).
     *
     * @return array
     */
    public function get_comments() {
        $dbh = $this->_db_connect();
        $stmt = $dbh->prepare(self::SQL_GET_COMMENTS);
        $stmt->bindValue(1, $this->id);
        $return_set = $stmt->execute();
        
        $id_list = array();
        while ( $row = $return_set->fetchArray() )
            array_push($id_list, $row['id']);

        return $id_list;
    }
}
?>
