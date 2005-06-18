<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_MODE=="BE")	{
		
  //	t3lib_extMgm::addModule("tools","txmocfilemanagerM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}

t3lib_extMgm::allowTableOnStandardPages("tx_mocfilemanager_files");
$TCA["tx_mocfilemanager_files"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files",		
		"label" => "file",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY uploaded",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_mocfilemanager_files.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "file, uploaded, type, mount,comment,downloads",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_mocfilemanager_mounts");

$TCA["tx_mocfilemanager_mounts"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_mounts",		
		"label" => "name",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_mocfilemanager_mounts.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "path, view, mountperm, name, text",
	)
);

//t3lib_extMgm::addLLrefForTCAdescr('tx_mocfilemanager_mounts',t3lib_extMgm::extPath($_EXTKEY).'locallang_csh.php');
t3lib_extMgm::addLLrefForTCAdescr('tx_mocfilemanager_mounts','typo3conf/ext/moc_filemanager/locallang_csh.php');


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout";
//$TCA["tt_content"]["types"]["list"]["showItems"] .= ",bodytext";

t3lib_extMgm::addPlugin(Array("LLL:EXT:moc_filemanager/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");



t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi2"]="layout,select_key";
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:moc_filemanager/flexform_pi1_ds.xml');

t3lib_extMgm::addPlugin(Array("LLL:EXT:moc_filemanager/locallang_db.php:tt_content.list_type_pi2", $_EXTKEY."_pi2"),"list_type");
?>