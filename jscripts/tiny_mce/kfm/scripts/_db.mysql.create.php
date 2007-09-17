<?php
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."directories(
		id INTEGER PRIMARY KEY auto_increment,
		name text,
		parent integer not null
	)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."files(
		id INTEGER PRIMARY KEY auto_increment,
		name text,
		directory integer not null,
		foreign key (directory) references ".$kfm_db_prefix."directories(id)
	)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."files_images(
		id INTEGER PRIMARY KEY auto_increment,
		caption text,
		file_id integer not null,
		width integer default 0,
		height integer default 0,
		foreign key (file_id) references ".$kfm_db_prefix."files (id)
	)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."files_images_thumbs(
		id INTEGER PRIMARY KEY auto_increment,
		image_id integer not null,
		width integer default 0,
		height integer default 0,
		foreign key (image_id) references ".$kfm_db_prefix."files_images (id)
	)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."parameters(
		name text,
		value text
	)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."session (
		`id` int(11) NOT NULL auto_increment,
		`cookie` varchar(32) default NULL,
		`last_accessed` datetime default NULL,
		PRIMARY KEY  (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."session_vars (
		`session_id` int(11) default NULL,
		`varname` text,
		`varvalue` text,
		KEY `session_id` (`session_id`),
		CONSTRAINT `".$kfm_db_prefix."session_vars_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `".$kfm_db_prefix."session` (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."tagged_files(
		file_id	INTEGER,
		tag_id	INTEGER,
		foreign key (file_id) references ".$kfm_db_prefix."files (id),
		foreign key (tag_id) references ".$kfm_db_prefix."tags (id)
	)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
	$kfmdb->query("CREATE TABLE ".$kfm_db_prefix."tags(
		id INTEGER PRIMARY KEY auto_increment,
		name text
	)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

	$kfmdb->query("insert into ".$kfm_db_prefix."parameters values('version','1.1.1')");
	$kfmdb->query("insert into ".$kfm_db_prefix."directories values(1,'',0)");
	if(!PEAR::isError($kfmdb))$db_defined=1;
?>
