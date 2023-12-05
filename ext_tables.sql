CREATE TABLE tx_instagram_account (
	name varchar(255) DEFAULT '' NOT NULL,
	username varchar(255) DEFAULT '' NOT NULL,
	app_id varchar(255) DEFAULT '' NOT NULL,
	app_secret varchar(255) DEFAULT '' NOT NULL,
	app_return_url varchar(255) DEFAULT '' NOT NULL,
	token_state tinyint(1) DEFAULT 0 NOT NULL,
);

CREATE TABLE tx_instagram_post
(
	caption text,
	media_type varchar(255) DEFAULT '' NOT NULL,
	media_url text,
	permalink varchar(255) DEFAULT '' NOT NULL,
	timestamp int(11) DEFAULT 0 NOT NULL,
	instagram_id varchar(255) DEFAULT '' NOT NULL,
	account int(11) DEFAULT 0 NOT NULL,
	image int(11) DEFAULT 0 NOT NULL,

	KEY account (account),
	KEY instagramId (instagram_id),
);
