CREATE TABLE IF NOT EXISTS users
(
	uid int NOT NULL auto_increment,
	username varchar(255) NULL,
	password varchar(255) NULL,
	email varchar(255) NULL,
	profile_pic varchar(255) NULL,
	status TEXT NULL,
	isVisible int unsigned DEFAULT 1,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(uid)
);

CREATE TABLE IF NOT EXISTS friends
(
	fr_id int NOT NULL auto_increment,
	uid_a int unsigned NULL,
	uid_b int unsigned NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(fr_id)
);