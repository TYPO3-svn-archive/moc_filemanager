<?php
class user_filehandler {
//  var $superroot="/mnt/disk2/CarlsbergFiles/";
//	var $superroot="/home/VonHallerFiles/";
	function user_filehandler() {
		global $TYPO3_CONF_VARS;
		$this->extconf = unserialize($TYPO3_CONF_VARS["EXT"]["extConf"]["moc_filemanager"]);
		$this->superroot = $this->extconf["SuperRoot"];
	}
	function processDatamap_postProcessFieldArray ($status, $table, $id, &$arr, &$parent) {
		/*
		 * MOC Filemanager
		 */
		if($table == "tx_mocfilemanager_mounts" && file_exists($this->cleanPath($this->superroot))) {
		//print("Test: ".t3lib_div::view_array($arr));
			$pid = $arr["pid"] ? $arr["pid"] : $this->getPidFromMountUid($id);
			if($status == "new") {
				$path_to_check = $this->cleanPath($this->superroot)."/".$this->cleanPath($arr["path"]);
				if(!file_exists($path_to_check)) {
					//print "The path $path_to_check does not exists, create it";
					$this->preparePathLine($arr["path"]);
					t3lib_div::mkdir($path_to_check);
				}
			}
			else {
				$mount = t3lib_BEfunc::getRecord("tx_mocfilemanager_mounts",$id,"uid,pid,path");
				//This is an update, check to see if path changes (Then it will be part of the $arr array
				$olddir = $this->cleanPath($this->superroot)."/".$this->cleanPath($mount["path"]);
				if($arr["path"]) {
					$this->preparePathLine($arr["path"]);
					$olddir = $this->cleanPath($this->superroot)."/".$this->cleanPath($mount["path"]);
					$newdir = $this->cleanPath($this->superroot)."/".$this->cleanPath($arr["path"]);
					if(file_exists($olddir)) {
						if(!file_exists($newdir)) {
							//print "Renaming $olddir to $newdir<br />";
							rename($olddir,$newdir);
							$this->pruneEmptyDirs($mount["path"]);
						}
					}
					else {
						print ("Creating $newdir since $olddir did not exist<br />");
						if(!file_exists($newdir)) {
							//print "The path $olddir does not exists, create it";
							t3lib_div::mkdir($newdir);
						}
					}
				}
				else {
					//The path was not changed, but it does not exist.
					if(!file_exists($olddir)) {
						$this->preparePathLine($mount["path"]);
						//print "The path $olddir does not exists, create it";
						t3lib_div::mkdir($olddir);
					}
				}
			}
		}
	}
	/**
	 *
	 *
	 */
	function pruneEmptyDirs($dir) {
		$to_check = explode("/",$dir);
		$count = count($to_check);
		for($i=($count);$i>0;$i--) {
      $the_rel_path = "";
      for($j=0;$j<($i);$j++) {
			$the_rel_path .=  "/".$to_check[$j];
      }
      $full_path=$this->cleanPath($this->superroot).$the_rel_path;
      if(file_exists($full_path)) {
			$files = t3lib_div::getFilesInDir($full_path,"",0,"mtime");
			$dirs = t3lib_div::get_dirs($full_path);
			//print "TEST: $the_rel_path has ".(count($files)+count($dirs))." files and dirs<br />";
			if(count($files)+count($dirs) == 0) {
				rmdir($full_path);
				//print "Deleting $full_path<br />";
			}
      }

		}
	}
  /**
   * This function makes sure that the whole path does exit.
   *
   */
	function preparePathLine($dir) {
		$to_check = explode("/",$dir);
		$count = count($to_check);
		for($i=0;$i<($count-1);$i++) {
			//      foreach($to_check as $d) {
			$the_rel_path .= "/".$to_check[$i];
			$fulldir = $this->cleanPath($this->superroot).$the_rel_path;
			//print "Checking if $fulldir exists ...";
			if(!file_exists($fulldir)) {
				//print "DOES NOT, CREATE<br />";
				t3lib_div::mkdir($fulldir);
			}
			else {
				//print "OK<br />";
			}
		}
	}
	/**
	 *
	 *
	 */
	function getPidFromMountUid($uid) {
		$mount = t3lib_BEfunc::getRecord("tx_mocfilemanager_mounts",$uid,"uid,pid");
		$pid= $mount["pid"];

		return $pid;
	}
	/**
	 *
	 *
	 */
	function cleanPath($path) {
		return ereg_replace('\/$','',$path);
	}
}
?>
