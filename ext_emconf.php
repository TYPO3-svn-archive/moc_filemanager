<?php

########################################################################
# Extension Manager/Repository config file for ext: "moc_filemanager"
# 
# Auto generated 17-06-2005 14:54
# 
# Manual updates:
# Only the data in the array - anything else is removed by next write
########################################################################

$EM_CONF[$_EXTKEY] = Array (
	'title' => 'MOC Fileshare manager',
	'description' => 'Filesharemanager for MOC Company',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'TYPO3_version' => '3.6.2-0.0.2',
	'PHP_version' => '0.0.2-0.0.2',
	'module' => 'mod1',
	'state' => 'beta',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Jan-Erik Revsbech',
	'author_email' => 'jer@moccompany.com',
	'author_company' => 'MOC Company',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'private' => 0,
	'download_password' => '',
	'version' => '0.7.1',	// Don't modify this! Managed automatically during upload to repository.
	'_md5_values_when_last_written' => 'a:108:{s:37:"#class.tx_mocfilemanager_tcemain.php#";s:4:"1d26";s:9:"ChangeLog";s:4:"c9ff";s:35:"class.tx_mocfilemanager_tcemain.php";s:4:"1d26";s:36:"class.tx_mocfilemanager_tcemain.php~";s:4:"7753";s:20:"defaulttemplate.tmpl";s:4:"c1be";s:21:"ext_conf_template.txt";s:4:"606c";s:22:"ext_conf_template.txt~";s:4:"cb48";s:12:"ext_icon.gif";s:4:"e9ec";s:17:"ext_localconf.php";s:4:"ce9c";s:14:"ext_tables.php";s:4:"31a5";s:14:"ext_tables.sql";s:4:"af0d";s:28:"ext_typoscript_constants.txt";s:4:"dc8c";s:28:"ext_typoscript_editorcfg.txt";s:4:"d48e";s:24:"ext_typoscript_setup.txt";s:4:"9e62";s:16:"flexform_ds.xml~";s:4:"6788";s:19:"flexform_pi1_ds.xml";s:4:"7b5e";s:22:"flexform_pi1_ds.xmlOLD";s:4:"7b5e";s:20:"flexform_pi1_ds.xml~";s:4:"7871";s:32:"icon_tx_mocfilemanager_files.gif";s:4:"dad9";s:33:"icon_tx_mocfilemanager_mounts.gif";s:4:"eabb";s:17:"locallang_csh.php";s:4:"e557";s:16:"locallang_db.php";s:4:"2060";s:7:"tca.php";s:4:"b884";s:11:"CVS/Entries";s:4:"0f1c";s:14:"CVS/Repository";s:4:"455e";s:8:"CVS/Root";s:4:"e101";s:19:"doc/wizard_form.dat";s:4:"2628";s:20:"doc/wizard_form.html";s:4:"c025";s:15:"doc/CVS/Entries";s:4:"c18a";s:18:"doc/CVS/Repository";s:4:"99e5";s:12:"doc/CVS/Root";s:4:"e101";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"c3af";s:14:"mod1/index.php";s:4:"439b";s:18:"mod1/locallang.php";s:4:"b7e7";s:22:"mod1/locallang_mod.php";s:4:"90ee";s:19:"mod1/moduleicon.gif";s:4:"dad9";s:16:"mod1/CVS/Entries";s:4:"e0f6";s:19:"mod1/CVS/Repository";s:4:"d250";s:13:"mod1/CVS/Root";s:4:"e101";s:35:"pi1/class.tx_mocfilemanager_pi1.php";s:4:"7cce";s:41:"pi1/class.tx_mocfilemanager_pi1.phpBACKUP";s:4:"96cc";s:13:"pi1/files.sql";s:4:"8b2e";s:17:"pi1/locallang.php";s:4:"e1d4";s:17:"pi1/mimetypes.php";s:4:"8530";s:15:"pi1/CVS/Entries";s:4:"e344";s:18:"pi1/CVS/Repository";s:4:"a41e";s:12:"pi1/CVS/Root";s:4:"e101";s:35:"pi2/class.tx_mocfilemanager_pi2.php";s:4:"d8df";s:38:"pi2/class.tx_mocfilemanager_pi2.phpOLD";s:4:"b8d9";s:17:"pi2/locallang.php";s:4:"78cb";s:15:"pi2/CVS/Entries";s:4:"c447";s:18:"pi2/CVS/Repository";s:4:"e62b";s:12:"pi2/CVS/Root";s:4:"e101";s:25:"res/class.filehandler.php";s:4:"37a6";s:26:"res/class.filehandler.php~";s:4:"37a6";s:29:"res/class.filemanager_div.php";s:4:"c6d0";s:30:"res/class.filemanager_div.php~";s:4:"b55c";s:19:"res/class.mount.php";s:4:"1bed";s:14:"res/delete.gif";s:4:"b428";s:18:"res/excel_ikon.gif";s:4:"d590";s:14:"res/folder.gif";s:4:"db56";s:20:"res/folder_small.gif";s:4:"7bee";s:17:"res/folder_up.gif";s:4:"0389";s:24:"res/joinbottom-large.gif";s:4:"10f9";s:17:"res/joinlarge.gif";s:4:"634c";s:14:"res/stats.html";s:4:"dc74";s:13:"res/test.html";s:4:"9191";s:14:"res/updater.pl";s:4:"c042";s:14:"res/updater.pm";s:4:"b069";s:17:"res/word_ikon.gif";s:4:"b8ca";s:15:"res/CVS/Entries";s:4:"8c5a";s:18:"res/CVS/Repository";s:4:"5c25";s:12:"res/CVS/Root";s:4:"e101";s:20:"res/fileicons/ai.gif";s:4:"0e3b";s:20:"res/fileicons/au.gif";s:4:"acb6";s:21:"res/fileicons/avi.gif";s:4:"335b";s:21:"res/fileicons/bmp.gif";s:4:"fd53";s:25:"res/fileicons/default.gif";s:4:"475a";s:21:"res/fileicons/doc.gif";s:4:"9a5b";s:21:"res/fileicons/exe.gif";s:4:"3e9f";s:23:"res/fileicons/flash.gif";s:4:"c584";s:24:"res/fileicons/folder.gif";s:4:"4209";s:21:"res/fileicons/gif.gif";s:4:"84ed";s:23:"res/fileicons/html1.gif";s:4:"5647";s:23:"res/fileicons/html2.gif";s:4:"20ed";s:23:"res/fileicons/html3.gif";s:4:"fe58";s:22:"res/fileicons/java.gif";s:4:"29b7";s:21:"res/fileicons/jpg.gif";s:4:"dee4";s:21:"res/fileicons/mov.gif";s:4:"0a20";s:22:"res/fileicons/mpeg.gif";s:4:"3669";s:21:"res/fileicons/pcd.gif";s:4:"b0d1";s:21:"res/fileicons/pcx.gif";s:4:"7d29";s:21:"res/fileicons/pdf.gif";s:4:"2bc7";s:22:"res/fileicons/php3.gif";s:4:"93aa";s:21:"res/fileicons/png.gif";s:4:"7dd7";s:21:"res/fileicons/sxc.gif";s:4:"ea41";s:21:"res/fileicons/sxw.gif";s:4:"b198";s:21:"res/fileicons/tga.gif";s:4:"e6d4";s:21:"res/fileicons/tif.gif";s:4:"460c";s:21:"res/fileicons/ttf.gif";s:4:"9af9";s:21:"res/fileicons/txt.gif";s:4:"25a1";s:21:"res/fileicons/wav.gif";s:4:"cf3e";s:21:"res/fileicons/xls.gif";s:4:"1ed5";s:21:"res/fileicons/zip.gif";s:4:"d8d5";s:25:"res/fileicons/CVS/Entries";s:4:"6734";s:28:"res/fileicons/CVS/Repository";s:4:"8531";s:22:"res/fileicons/CVS/Root";s:4:"e101";}',
);

?>