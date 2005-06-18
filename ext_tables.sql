#
# Table structure for table 'tx_mocfilemanager_files'

CREATE TABLE tx_mocfilemanager_log (
	id int(11) unsigned NOT NULL auto_increment,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	action tinytext NOT NULL,
	fullpath mediumtext NOT NULL,
	mount int(11) unsigned DEFAULT '0' NOT  NULL,
	size int(11) unsigned DEFAULT '0' NOT NULL,
	user int(11) unsigned DEFAULT '0' NOT NULL,
	ip_add tinytext NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE tx_mocfilemanager_files (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	file tinytext NOT NULL,
	uploaded int(11) DEFAULT '0' NOT NULL,
	type int(11) unsigned DEFAULT '0' NOT NULL,
	mount int(11) unsigned DEFAULT '0' NOT NULL,
	size int(11) unsigned DEFAULT '0' NOT NULL,
	comment text NOT NULL,
	downloads int(11) unsigned DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_mocfilemanager_mounts'
#
CREATE TABLE tx_mocfilemanager_mounts (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,
	path tinytext NOT NULL,
	mountperm blob NOT NULL,
	writeperm blob NOT NULL,
	name tinytext NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);



