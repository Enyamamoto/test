CREATE TABLE `posts`(
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`message` text NOT NULL,
`del_flg` tinyint(1) DEFAULT 0,
`created` datetime,
`modified` datetime,
PRIMARY KEY(id)
);

CREATE TABLE `comments`(
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`post_id` int(10),
`comment_name` varchar(255) NOT NULL,
`comment_password` varchar(255) NOT NULL,
`comment` text NOT NULL,
`del_flg` tinyint(1) DEFAULT 0,
`created` datetime,
`modified` datetime,
PRIMARY KEY(id)
);

