CREATE TABLE authors (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    name     VARCHAR(20),
    username VARCHAR(20),
    password VARCHAR(255)
);

CREATE TABLE posts (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    author_id      INTEGER,
    content        TEXT,
    insert_date    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    parent_post_id INTEGER DEFAULT 0,
    root_post_id   INTEGER DEFAULT 0,
    
    FOREIGN KEY(author_id)      REFERENCES authors(id),
    FOREIGN KEY(parent_post_id) REFERENCES posts(id),
    FOREIGN KEY(root_post_id)   REFERENCES posts(id)
);
