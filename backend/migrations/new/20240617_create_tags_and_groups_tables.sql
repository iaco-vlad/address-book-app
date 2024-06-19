CREATE TABLE groups_table
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE tags
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE contacts_groups
(
    contact_id    INT,
    group_id      INT,
    main_group_id INT DEFAULT NULL,
    FOREIGN KEY (contact_id) REFERENCES address_book (id),
    FOREIGN KEY (group_id) REFERENCES groups_table (id) ON DELETE CASCADE,
    FOREIGN KEY (main_group_id) REFERENCES groups_table (id) ON DELETE CASCADE,
    INDEX contact_id_index (contact_id),
    INDEX group_id_index (group_id),
    INDEX main_group_id_index (main_group_id),
    UNIQUE INDEX unique_contact_group_pair (contact_id, group_id)
);

CREATE TABLE contacts_tags
(
    contact_id INT,
    tag_id     INT,
    FOREIGN KEY (contact_id) REFERENCES address_book (id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE,
    INDEX contact_id_index (contact_id)
);

CREATE TABLE groups_groups
(
    parent_group_id INT,
    child_group_id  INT,
    FOREIGN KEY (parent_group_id) REFERENCES groups_table (id) ON DELETE CASCADE,
    FOREIGN KEY (child_group_id) REFERENCES groups_table (id) ON DELETE CASCADE,
    UNIQUE INDEX unique_group_pair (parent_group_id, child_group_id)
);