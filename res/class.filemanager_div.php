<?php
/*************
 * Changelog
 *
 *  17/06-2005: Jan-erik Revsbech
 *   Fixed problem with not setting type and pid when updating from filesystem.
 *
 */

class filemanager_div {
	var $FilesStoragePID = 0;
	var $MountsStoragePid = 0;
	function filemanager_div() {
		$this->dbObj = $GLOBALS["TYPO3_DB"];
	}
	function test() {
		return "Im div";
	}
	/*
	 * Returns information stored in database about files on local harddrive. If a file record is not found, a new one is created.
   */
	function getAdditionalInfo(&$mount,$file) {
		$file=$this->removeFirstSlash($file);
		$res = $this->dbObj->exec_SELECTquery("downloads,comment,uploaded,size,users.name","tx_mocfilemanager_files as files LEFT JOIN fe_users as users ON files.cruser_id=users.uid","file='".$this->dbObj->quoteStr($file,"tx_mocfilemanager_files")."' AND mount='".intval($mount->uid)."'");
    if($res && $this->dbObj->sql_num_rows($res)) {
		 $row = $this->dbObj->sql_fetch_assoc($res);
		 $this->dbObj->sql_free_result($res);
    }
    else {
		 //Seems that there is a record on the filesystem, that does not exists in the DB.
		 $info = $this->insertDBEntryFromFilesystem($file,$mount);
		 return array("size"=>$info["size"],
						  "comment"=>"",
						  "name"=>"",
						  "uploaded"=>$info["mtime"],
						  "downloads"=>0);
    }
    return $row;
	}
	/**
	 *
	 * FUNCTION insertDBEntryFromFilesystem($file,&$mount)
   *
   */
	function insertDBEntryFromFilesystem($file,$mount) {
		//Seems that there is a record on the filesystem, that does not exists in the DB.
		$info = @stat($mount->getDir()."/".$file);
		if(is_file($mount->getDir()."/".$file)) {
			$this->dbObj->exec_INSERTquery('tx_mocfilemanager_files',array("file" => $this->dbObj->quoteStr($file,'tx_mofilemanager_files'),
																								"size"=>$info["size"],
																								"uploaded"=>$info["mtime"],
																								"mount" => intval($mount->uid),
																								"type" => 0,
																								"pid" => $this->FilesStoragePID
													 )
				);
		}
		elseif(is_dir($mount->getDir()."/".$file)) {
			$this->dbObj->exec_INSERTquery('tx_mocfilemanager_files',array("file" => $this->dbObj->quoteStr($file,'tx_mofilemanager_files'),
																								"size"=>0,
																								"uploaded"=>$info["mtime"],
																								"mount" => intval($mount->uid),
																								"type" => 1,
																								"pid" => $this->FilesStoragePID
														 )
				     );
		}
		return $info;
	}
	/**
	 * Writes an entry in the filemanager log
	 */
	function log($action,$path,$mountId,$size) {
		$this->dbObj->exec_INSERTquery('tx_mocfilemanager_log',array(
													 'tstamp'=>time(),
													 'action'=>$action,
													 'fullpath'=>$this->dbObj->quoteStr($path,'tx_mocfilemanager_log'),
													 'mount'=>$mountId,
													 'size'=>$size,
													 'user'=>$GLOBALS['TSFE']->fe_user->user['uid'],
													 'ip_add'=>t3lib_div::getIndpEnv('REMOTE_ADDR')
													 )
			);
/*

*/
	}
	/**
	 * Removes the first slash if its there.
	 *
	 *
	 */
	function removeFirstSlash($file) {
		if(substr($file,0,1) == '/') {
			$file = substr($file,1);
		}
		return $file;
	}
	/* *****************************************************
	 *
	 *  FUNCTION clear_dir
	 *
	 */
	function clean_dir($thedir) {
		return ereg_replace("[\/\. ]*$","",$thedir);		// Removes all dots, slashes and spaces after a path...
	}
}

?>