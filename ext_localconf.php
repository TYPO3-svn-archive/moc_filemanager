<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addPageTSConfig('
	#Default TSconfig
');
t3lib_extMgm::addUserTSConfig('
	Default User TScondig
');
t3lib_extMgm::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_mocfilemanager_mounts", field "text"
	# ***************************************************************************************
RTE.config.tx_mocfilemanager_mounts.text {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_mocfilemanager_pi1 = < plugin.tx_mocfilemanager_pi1.CSS_editor
",43);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_mocfilemanager_pi1.php","_pi1","list_type",0);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_mocfilemanager_pi2 = < plugin.tx_mocfilemanager_pi2.CSS_editor
",43);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi2/class.tx_mocfilemanager_pi2.php","_pi2","list_type",0);

/* ************************ Using hooks ************************* */
$extconf = unserialize($TYPO3_CONF_VARS["EXT"]["extConf"]["moc_filemanager"]);
if($extconf["AutoDirCreation"]) {
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:moc_filemanager/res/class.filehandler.php:user_filehandler';
}

?>