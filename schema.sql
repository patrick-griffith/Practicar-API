CREATE TABLE stories_templates (
    id int(10) unsigned NOT NULL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    urlkey VARCHAR(100) NOT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    UNIQUE (urlkey)
);

INSERT INTO stories_templates (id,name,urlkey)
VALUES
    (1,'Blogs','blogs'),
    (2,'Case Studies','casestudies');

CREATE TABLE stories_templates_sections (
    id int(10) unsigned NOT NULL PRIMARY KEY,
    stories_templates_id int(10) unsigned NOT NULL,
    name VARCHAR(50) NOT NULL,
    title VARCHAR(255) NULL,
    text_placedholder TEXT NULL,
    ordinal int unsigned NOT NULL DEFAULT 0,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    FOREIGN KEY (stories_templates_id) REFERENCES stories_templates(id)
);

INSERT INTO stories_templates_sections (id,stories_templates_id,name,title,text_placedholder,ordinal)
VALUES 
    (1,1,'blogtitle','','Enter blog content here.',0),
    (2,2,'summary','Summary','Give an overview of the work you did.',0),
    (3,2,'outcomes','Outcomes','What were the results of the work you did.',1);

CREATE TABLE stories (
    id int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    stories_templates_id int(10) unsigned NOT NULL,
    title VARCHAR(255) NOT NULL,
    urlkey VARCHAR(100) NOT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    FOREIGN KEY (stories_templates_id) REFERENCES stories_templates(id),
    UNIQUE (stories_templates_id, urlkey)
);

CREATE TABLE stories_sections (
    id int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    stories_id int(10) unsigned NOT NULL,
    stories_templates_sections_id int(10) unsigned NOT NULL,
    title VARCHAR(255) NOT NULL,
    text TEXT,
    image_files_id int(10) unsigned,
    ordinal int unsigned NOT NULL DEFAULT 0,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    FOREIGN KEY (stories_id) REFERENCES stories(id),
    FOREIGN KEY (image_files_id) REFERENCES files(id),
    FOREIGN KEY (stories_templates_sections_id) REFERENCES stories_templates_sections(id)
);

CREATE TABLE providers_stories (
    providers_id int(10) unsigned NOT NULL,
    stories_id int(10) unsigned NOT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (stories_id, providers_id),
    FOREIGN KEY (stories_id) REFERENCES stories(id),
    FOREIGN KEY (providers_id) REFERENCES providers(id)
);

CREATE TABLE providers_clients_stories (
    providers_clients_id int(10) unsigned NOT NULL,
    stories_id int(10) unsigned NOT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (stories_id, providers_clients_id),
    FOREIGN KEY (stories_id) REFERENCES stories(id),
    FOREIGN KEY (providers_clients_id) REFERENCES providers_clients(id)
);
