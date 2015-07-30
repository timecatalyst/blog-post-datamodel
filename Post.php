<?php

/**
 * Abstract class for creating and loading blog posts.
 */
abstract class Post {
    const SQL_LOAD_POST     = "SELECT authors.name AS author_name, posts.* FROM authors JOIN posts ON posts.author_id = authors.id WHERE posts.id = ?";
    const SQL_SAVE_NEW      = "INSERT INTO posts (author_id, content, parent_post_id, root_post_id) VALUES (?, ?, ?, ?)";
    const SQL_SAVE_EXISTING = "UPDATE posts SET content = ? WHERE id = ?";

    protected $id;
    protected $author;
    protected $author_id;
    protected $content;
    protected $insert_date;
    protected $parent_post_id;
    protected $root_post_id;
    protected $db_file = "blog_db.sqlite";
    
    /**
     * Returns the Post's backstore ID
     * @return unsigned int
     */
    public function get_id()             { return $this->id; }

    /**
     * Returns the Post author's name
     * @return string
     */
    public function get_author()         { return $this->author; }

    /**
     * Returns the Post author's backstore ID
     * @return unsigned int
     */
    public function get_author_id()      { return $this->author_id; }

    /**
     * Returns the Post content.
     * @return string
     */
    public function get_content()        { return $this->content; }

    /**
     * Returns backstore insert timestamp.
     * @return string
     */
    public function get_insert_date()    { return $this->insert_date; }

    /**
     * Returns the Post ID of the object's parent.  A value of 0 indicates no 
     * parent.
     * @return unsigned int
     */
    public function get_parent_post_id() { return $this->parent_post_id; }

    /**
     * Returns the Post ID of the object's root parent.  A value of 0 indicates 
     * no root parent.
     * @return unsigned int
     */
    public function get_root_post_id()   { return $this->root_post_id; }
    
    /**
     * Sets the content of the Post.
     *
     * @param string $content
     */
    public function set_content($content) { $this->content = $content; }
    
    /**
     * Constructor.  Creates a new Post object or loads an existing object from 
     * backstore.
     *
     * @param array $args An associative array containing one or more of the 
     *                    following keys:
     *     - db_file        : Optional. Specify a custom sqlite3 file.
     *     - id             : Tells the constructor to load an existing object.
     *     - author_id      : An integer that represents a user in the backstore.
     *     - content        : The post content.
     *     - parent_post_id : The parent Post associated with the current Post.
     *     - root_post_id   : The root parent Post associate with the current Post.
     *
     * @return Post
     */
    protected function __construct(array $args) {
        if ( isset($args['db_file']) ) 
            $this->db_file = $args['db_file'];

        if ( isset($args['id']) ) {
            $this->_load_post($args['id']);
            return;
        }

        if ( isset($args['author_id']) ) 
            $this->author_id = $args['author_id'];
        else
            die("Must supply an author_id");
            
        if ( $args['content'] )
            $this->content = $args['content'];

        if ( isset($args['parent_post_id']) ) {
            $this->parent_post_id = $args['parent_post_id'];
            $this->root_post_id   = $args['parent_post_id'];
        }
            
        if ( isset($args['root_post_id']) )
            $this->root_post_id = $args['root_post_id'];
    }

    protected function _db_connect($db_file = null) { 
        if ( !$db_file ) 
            $db_file = $this->db_file;
        return new SQLite3($db_file);
    }
    
    protected function _load_post($id) {
        $dbh = $this::_db_connect();
        $stmt = $dbh->prepare(self::SQL_LOAD_POST);
        $stmt->bindValue(1, $id);
        
        $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        
        $this->id             = $row['id'];
        $this->author         = $row['author_name'];
        $this->author_id      = $row['author_id'];
        $this->content        = $row['content'];
        $this->insert_date    = $row['insert_date'];
        $this->parent_post_id = $row['parent_post_id'];
        $this->root_post_id   = $row['root_post_id'];
    }

    /**
     * Save the current object to the backstore.
     */
    public function save() {
        return $this->id ? $this->_save_existing()
                         : $this->_save_new();
    }
    
    protected function _save_new() {
        $dbh = $this->_db_connect();
        $stmt = $dbh->prepare(self::SQL_SAVE_NEW);

        $parent = $this->parent_post_id ? $this->parent_post_id : 0;
        $root   = $this->root_post_id   ? $this->root_post_id   : 0;

        $stmt->bindValue(1, $this->author_id);
        $stmt->bindValue(2, $this->content);
        $stmt->bindValue(3, $parent);
        $stmt->bindValue(4, $root);
        $stmt->execute();

        $this->_load_post($dbh->lastInsertRowID());
    }

    protected function _save_existing() {
        $dbh = $this->_db_connect();
        $stmt = $dbh->prepare(self::SQL_SAVE_EXISTING);
        $stmt->bindValue(1, $this->content);
        $stmt->bindValue(2, $this->id);
        $stmt->execute();

        $this->_load_post($this->id);
    }
}
?>
