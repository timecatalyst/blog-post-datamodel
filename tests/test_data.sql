-- Passwords are all set to "password"
INSERT INTO authors (id, name, username, password) VALUES (1, "Blog Author1", "bauth1", "$2y$10$/WLrS4YPb0QM/i31E/8UieV6ydf0DsYlqtqUEaiBUiCifD2NGUfKW");
INSERT INTO authors (id, name, username, password) VALUES (2, "Blog Author2", "bauth2", "$2y$10$/WLrS4YPb0QM/i31E/8UieV6ydf0DsYlqtqUEaiBUiCifD2NGUfKW");
INSERT INTO authors (id, name, username, password) VALUES (3, "Commenter 1", "comm1", "$2y$10$/WLrS4YPb0QM/i31E/8UieV6ydf0DsYlqtqUEaiBUiCifD2NGUfKW");
INSERT INTO authors (id, name, username, password) VALUES (4, "Commenter 2", "comm2", "$2y$10$/WLrS4YPb0QM/i31E/8UieV6ydf0DsYlqtqUEaiBUiCifD2NGUfKW");
INSERT INTO authors (id, name, username, password) VALUES (5, "Commenter 3", "comm3", "$2y$10$/WLrS4YPb0QM/i31E/8UieV6ydf0DsYlqtqUEaiBUiCifD2NGUfKW");
INSERT INTO authors (id, name, username, password) VALUES (6, "Commenter 4", "comm4", "$2y$10$/WLrS4YPb0QM/i31E/8UieV6ydf0DsYlqtqUEaiBUiCifD2NGUfKW");

INSERT INTO posts (id, author_id, content) VALUES (1, 1, "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.");
INSERT INTO posts (id, author_id, content) VALUES (2, 2, "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.");
INSERT INTO posts (id, author_id, content) VALUES (3, 1, "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.");

INSERT INTO posts (id, author_id, content, parent_post_id, root_post_id) VALUES (4, 3, "Negative comment", 1, 1);
INSERT INTO posts (id, author_id, content, parent_post_id, root_post_id) VALUES (5, 4, "Another Negative comment", 1, 1);
INSERT INTO posts (id, author_id, content, parent_post_id, root_post_id) VALUES (6, 5, "Positive retort", 5, 1);
INSERT INTO posts (id, author_id, content, parent_post_id, root_post_id) VALUES (7, 3, "Another Positive retort", 5, 1);
INSERT INTO posts (id, author_id, content, parent_post_id, root_post_id) VALUES (8, 4, "Trollish reply", 6, 1);

INSERT INTO posts (id, author_id, content, parent_post_id, root_post_id) VALUES (9, 6, "Bot response", 2, 2);
INSERT INTO posts (id, author_id, content, parent_post_id, root_post_id) VALUES (10, 6, "More spam", 2, 2);
