<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Jan-Erik Revsbech (jer@moccompany.com)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Fileshare manager' for the 'moc_filemanager' extension.
 *
 * @author	Jan-Erik Revsbech <jer@moccompany.com>
 *
 *
 * WARNING: This version is modified to Suit the Carlsberg needs!!!!
 *
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once(PATH_t3lib."class.t3lib_basicfilefunc.php");
require_once(PATH_t3lib."class.t3lib_extfilefunc.php");
require_once(t3lib_extMgm::extPath("moc_filemanager")."/res/class.mount.php");
require_once(t3lib_extMgm::extPath("moc_filemanager")."/res/class.filemanager_div.php");

class tx_mocfilemanager_pi1 extends tslib_pibase {
	var $prefixId = "tx_mocfilemanager_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_mocfilemanager_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "moc_filemanager";	// The extension key.

	/**
	 * MAIN Called by TYPO3 core engine
	 */
	function main($content,$conf)	{

		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		$this->transferFlexToData();


		$this->div = new filemanager_div();
		$this->dbObj = $GLOBALS['TYPO3_DB'];
		$this->cInfo = t3lib_div::clientInfo();
		//For now this is only for Internet explorer!

		//This is taken out!!
		$this->canUseLayers = ($this->cInfo['BROWSER'] == 'msie' && false ? 1 : 0) ;
		$this->recursive = $this->cObj->stdWrap($this->conf["recursive"],$this->conf["recursive."]);;
		$this->mode = strtoupper($this->cObj->stdWrap($this->conf["CODE"],$this->conf["CODE."]));

		$this->asExplorer = 1;

		//**************** DETERMINE WHERE TO GET FOUNTS FROM *******************
		$this->selectFrom = strtoupper($this->cObj->stdWrap($this->conf["selectFrom"],$this->conf["selectFrom."]));
		$this->from = $this->cObj->stdWrap($this->conf["from"],$this->conf["from."]);

		list($filter["code"],$filter["mount"],$filter["dir"]) = split('[|]', $this->mode);
		$this->mode = $filter["code"];

		$this->mountPID = $this->pi_getPidList($this->cObj->stdWrap($this->conf["mountStoragePID"],$this->conf["mountStoragePID."]),$this->recursive);
		$this->filesPID = $this->cObj->stdWrap($this->conf["filesStoragePID"],$this->conf["filesStoragePID."]);
		$this->maxDepth = $this->cObj->stdWrap($this->conf["maxDepth"],$this->conf["maxDepth."]);

		$this->div->FilesStoragePID = $this->filesPID;
		$this->div->MountsStoragePID = $this->cObj->stdWrap($this->conf["mountStoragePID"],$this->conf["mountStoragePID."]);

		$this->showFilesPID = $this->cObj->stdWrap($this->conf["showFilesPID"],$this->conf["showFilesPID."]);
		if(!($this->showFilesPID)) {
			$this->showFilesPID = $GLOBALS["TSFE"]->id;
		}

		//This is a super hack, sorry for that, but It was the fastest way to solve the problem
		if($conf["superroot"]) {
		  $this->documentRoot = $this->div->clean_dir($conf["superroot"].$this->cObj->stdWrap($conf["documentRoot"],$conf["documentRoot."]));
		}
		else {
		  $this->documentRoot = $this->div->clean_dir($this->cObj->stdWrap($conf["documentRoot"],$conf["documentRoot."]));
		}

		$this->wraps=array();
		$this->Activewraps=array();
		$this->ActiveATagParams = array();
		$this->ATagParams = array();
		for($i = 0; $i <$this->maxDepth+1; $i++) {
		  array_push($this->wraps,$this->conf["levels."]["level$i."]["wrap"]);
		  array_push($this->Activewraps,$this->conf["levels."]["level$i."]["ACT."]["wrap"]);
		  if($this->conf["levels."]["level$i."]["ATagParams"])
		    array_push($this->ATagParams,$this->conf["levels."]["level$i."]["ATagParams"]);
		  else
		    array_push($this->ATagParams,'class="filelink"');
		  if($this->conf["levels."]["level$i."]["ACT."]["ATagParams"])
		    array_push($this->ActiveATagParams,$this->conf["levels."]["level$i."]["ACT."]["ATagParams"]);
		  else
		    array_push($this->ActiveATagParams,'class="filelinkActive"');
		}
		$this->mountwrap = $this->conf["mountwrap"];
		$this->asExplorer = $this->conf["asExplorer"] ? 1:0;

		if($this->mode == "UPLOAD_ONLY") {
		  if($this->from) {
		    $temp = explode(",",$this->from);
		    $this->mountID = $temp[0] ;	// hard coded mount var stored in CODE parameter
		  }
		}
		else {
		  $this->mountID = t3lib_div::GPvar("mountpoint");
		}
		$filename=t3lib_div::GPvar("filename");
		//If this is set, hten a file is uploaded along the request.
		$uploadfile=t3lib_div::GPvar("uploadfile");
		//If this is set, then the user asked to make a directory.
		$mkdir=t3lib_div::_GP("mkdir");

		// Get the mountpoints.
		if($this->selectFrom == "CHOOSE") {
		  //Mountpoints are chosen manually
		}

		if($this->selectFrom == "CHOOSE" || $this->selectFrom == "PAGES" || $this->selectFrom == "THISPAGE") {
		  $this->mountlist = array();
		  if($this->selectFrom == "CHOOSE")
		    $mountlist =$this->from;
		  if($this->selectFrom == "PAGES") {
		    $mountlist = $this->getAllMountIdsInPages($this->pi_getPidList($this->cObj->stdWrap($this->conf["mountStoragePID"],$this->conf["mountStoragePID."]),$this->recursive));
		  }
		  if($this->selectFrom == "THISPAGE")
		    $mountlist = $this->getAllMountIdsInPages($GLOBALS['TSFE']->id);
		  foreach(explode(",",$mountlist) as $mp) {
		    if($mp == $this->mountID) {
		      $this->mount = new mount(intval($mp),$this->documentRoot);
		      $this->mountlist[] = &$this->mount;
		    }
		    else {
		      $this->mountlist[] = new mount(intval($mp),$this->documentRoot);
		    }
		  }
		  //Make a check. If no mount is specified, make the first mount i mountlist the active one. Unless were in LISTDIRS mode, then there is no active mount.
		  if(!$this->mount && $this->mode != "LISTDIRS") {
		    $this->mount = &$this->mountlist[0];
		  }
		}
		else {
		  //Default is to take it from piVars
		  $this->mount = new mount($this->mountID,$this->documentRoot);
		  $this->mountlist[] = &$this->mount;
		}
		$this->filehandler_mps=array();
		$this->initSingleMountPoint($this->mount);
		$this->initFilehandler();

		$reldir  = ereg_replace("^/","",t3lib_div::GPvar("dir"));
		if($this->mount) {
		  $dir=$this->div->clean_dir($this->mount->getDir()."/".$reldir);
		}
		else {
		  $dir = "";
		}
		// The rootline of files (The path broken up essentially)
		$this->filelistRootLine = explode("/",$reldir);
		$task=t3lib_div::GPvar("task");

		//Init done.

		/*
		 *
		 *  #######################  LISTDIRS #######################
		 *
		 */
		if($this->mode == "LISTDIRS") {
		  foreach($this->mountlist as $mp) {
		    if($this->hasAccess($mp,$dir,"read")) {
		      $tempstr = "";
		      if($this->asExplorer) {
			$tempstr .= "<table class='mounttable' border=0 cellpadding=0 cellspacing=0>";
		      }
		      // Cleaning up the path, so its nice and tidy:)

		      $basedir = $mp->getDir();

		      $param["parameter"]=$this->showFilesPID;
		      $param["additionalParams"]="&mountpoint=".$mp->uid;

		      if($this->asExplorer) {
			$tempstr .= '<tr><td><img src="t3lib/gfx/i/_icon_ftp.gif" width="18" height="16" align="top">';
		      }
		      //		    if($this->mount->uid == $row["uid"]) {
		      if($this->mount->uid == $mp->uid) {
			$param["ATagParams"]=$this->ActiveATagParams[0];
			//		      $tempstr .= $this->cObj->wrap($this->cObj->typolink($row["name"],$param),$this->Activewraps[0]);
			$tempstr .= $this->cObj->wrap($this->cObj->typolink($mp->getName(),$param),$this->Activewraps[0]);
			if($this->asExplorer) {
			  $tempstr .="</td></tr>";
			}
			//		      $tempstr .= $this->listdirs($row["uid"],$basedir,$reldir);
			$tempstr .= $this->listdirs($mp->uid,$basedir,$reldir);
		      }
		      else {
			$param["ATagParams"]=$this->ATagParams[0];
			//		      $tempstr .= $this->cObj->wrap($this->cObj->typolink($row["name"],$param),$this->wraps[0]);
			$tempstr .= $this->cObj->wrap($this->cObj->typolink($mp->getName(),$param),$this->wraps[0]);
			if($this->asExplorer) {
			  $tempstr .="</td></tr>";
			}
		      }
		      if($this->asExplorer) {
			$tempstr .= "</table>";
		      }
		      $content .= $this->cObj->wrap($tempstr,$this->mountwrap);
		    }
		  }
		  return $this->pi_wrapInBaseClass($this->cObj->wrap($content,$this->conf["allWrap"]));
		}

		/**
		 *
		 *  #######################  VIEW #######################
		 *
		 */
		if($this->mode == "VIEW") {
		  /*
		   *
		   * DEFAULT Page
		   * Show if no task and directory is givven.
		   *
		   */
		  if(empty($reldir) && $this->conf["showDesc"]) {
                   $pars["parseFunc"] = 1;
		   $pars["parseFunc."] = $this->conf["parseFunc."];
		   $content .= $this->cObj->stdWrap($this->mount->data["text"],$pars);
		  }
		  /*
		   *
		   *  TASK = download
		   *
		   */
		  if(!empty($filename) && ($task == "download")) {
		    if(!($this->hasAccess($this->mount,$dir,"read"))) {
		      $content = '<p class="error">'.$this->pi_getLL("noreadaccessfordownload").'</p>';
		      return $this->pi_wrapInBaseClass($content);
		    }
		    $this->dbObj->sql_query("UPDATE tx_mocfilemanager_files SET downloads=downloads + 1 WHERE file='".addslashes($this->div->removeFirstSlash($reldir."/".$filename))."' AND mount=".intval($this->mount->uid));

//	function log($action,$path,$mountId,$size) {
			 $this->div->log('DOWNLOAD',"$dir/$filename",$this->mount->uid,filesize("$dir/$filename"));

		    //    $file=$this->removeFirstSlash($file);


		    //Clean all output buffers.
		    while (@ob_end_clean());
		    //Set time limit to value given in conf
		    if($this->conf["timeLimit"]>0) {
		      set_time_limit($this->conf["timelimit"]);
		    }
		    if($this->conf["killChild"]) {
		      apache_child_terminate();
		    }
		    // including mimetypes.php here means it is only included when required, thereby saving parsing time for all other requests
		    include("mimetypes.php");
		    // extract the file extesion and attempt to determine the Mime type from the file's extension
		    $fileinfo = t3lib_div::split_fileref($filename);
		    $mimetype = $mimetypes[$fileinfo["fileext"]];
		    if ($mimetype == "") {
		      $mimetype = "application/octet-stream";
		    }
		    $name = "$dir/$filename";
		    //		    return "Name: $dir/$filename";
		    $fp = fopen($name, 'rb');
		    // send the right headers
		    header("Cache-control: must-revalidate, post-check=0, pre-check=0");
		    header("Content-Transfer-Encoding: binary");
		    header("Content-Type: $mimetype");
		    header("Content-Length: ".filesize($name));
		    header("Content-Disposition: attachment; filename=$filename");
		    header("Cache-control: private");
		    while (!feof($fp)) {
		      $buffer = fgets($fp, 4096);
		      echo $buffer;
		    }
		    fclose($fp);
		    if($this->conf["timeLimit"])
		      set_time_limit(0);
		    exit;
		  }
		  /*
		   *
		   *  TASK = delete
		   *
		   *
		   * 		   */
		  if($task == "delete") {
		    if(t3lib_div::GPvar("confirmed") == "yes") {
				 $this->div->log('DELETE',"$dir/$filename",$this->mount->uid,filesize("$dir/$filename"));
				 if($this->filehandler->func_delete(array("data"=>"$dir/$filename"))) {
					 $this->remove_file_entry("$reldir/$filename");
					 $relative_url = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',array("mountpoint"=>$this->mountID,
																												"dir"=>$reldir));
					 header("Location: http://" . $_SERVER['HTTP_HOST']
							  . $this->div->clean_dir(dirname($_SERVER['PHP_SELF']))
							  . "/" . $relative_url);
				 }
				 else {
					 $content .= "<p class='error'>".$this->pi_getLL("error")."</p>";
					 //$content .= "Du ønskede at slette filen $dir/$filename";
				 }
		    }
		    else {
				 // This could be a javascript confirmation.
				 $content .= "<p class='notice'>".$this->pi_getLL("areyousure")."<br><br><b>".$this->mount->getName().":$reldir/$filename</b><br><br></p><p class='notice'> <b>".$this->pi_getLL("noregret")."</b></p>";
				 $content .= "<form method ='POST' action='".$this->cObj->currentPageUrl()."'>\n";
				 $content .= "<input type='hidden' name='dir' value='$reldir'>\n";
				 $content .= "<input type='hidden' name='mountpoint' value='".$this->mount->uid."'>\n";
				 $content .= "<input type='hidden' name='task' value='delete'>\n";
				 $content .= "<input type='hidden' name='confirmed' value='yes'>\n";
				 $content .= "<input type='hidden' name='filename' value='$filename'>\n";
				 $content .= "<input type='Submit' value='".$this->pi_getLL("deletebutton")."' class='actionbutton'></form>\n";
				 //return $content;
				 return $this->pi_wrapInBaseClass($content);
		    }
		  }
		  /*
		   *
		   *  TASK = RENAME
		   *
		   */
		  if($task == "rename") {
		    $newname = t3lib_div::GPvar("newname");
		    //		    $mountpoint = t3lib_div::GPvar("mountpoint");
		    $oldname = t3lib_div::GPvar("oldname");
		    if($newname) {
		      if($this->hasAccess($this->mount,$dir,"upload")) {
			if($newname != $oldname) {

			  $test = $this->filehandler->func_rename(array("target"=>"$dir/$oldname","data"=>$newname));
			  if(!$test) {
			    return "Error renaming";
			  }
			  $fileinfo = t3lib_div::split_fileref($test);
			  $newname = $fileinfo["file"];

			}
			$comment = t3lib_div::GPvar("comment");
			$this->rename_file_entry("$reldir/$oldname","$reldir/$newname",$this->mount->uid,$comment);
			$relative_url = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',array("mountpoint"=>$this->mountID,
											      "dir"=>$reldir));
			header("Location: http://" . $_SERVER['HTTP_HOST']
			       . $this->div->clean_dir(dirname($_SERVER['PHP_SELF']))
			       . "/" . $relative_url);
			exit();
		      }
		      else {
			return $this->pi_wrapInBaseClass("<p class='error'>".$this->pi_getLL("noaccessrename")."</p>");
		      }
		    }
		    else {
		      // Render the rename form
		      $content .= "<p class='tekst'>".$this->pi_getLL("rename")." <b>$oldname</b> ".$this->pi_getLL("to")." </p>";
		      $content .= '<FORM method="POST" action="'.$this->cObj->currentPageUrl().'">';
		      $content .= "<input type=\"hidden\" name=\"task\" value=\"rename\">\n";
		      $content .= "<input type=\"hidden\" name=\"mountpoint\" value=\"".$this->mount->uid."\">\n";
		      $content .= "<input type=\"hidden\" name=\"oldname\" value=\"$oldname\">\n";
		      $content .= "<input type=\"hidden\" name=\"dir\" value=\"$reldir\">\n";
		      $content .= "<input type=\"text\" size=\"40\" class=\"action\" name=\"newname\" value=\"$oldname\">\n";
		      $content .= "<input type=\"submit\" class=\"action\" value=\"".$this->pi_getLL("renameButton")."\">\n";
		      //$info = $this->getAdditionalInfo($this->mount,$reldir."/".$oldname);
		      $info = $this->div->getAdditionalInfo($this->mount,$reldir."/".$oldname);
		      if($this->conf["useDescriptions"]) {
			$content .= '<br /><textarea name="comment" cols="40" rows ="5" >'.$info["comment"].'</textarea>';
		      }
		      $content .= "</form>";
		      //return $content;
		      return $this->pi_wrapInBaseClass($content);
		    }
		  }
		  /*
		   *
		   *  TASK = MKDIR
		   *
		   */
		  //		  if($task == "mkdir") {
		  if(!empty($mkdir)) {
		    $comment = t3lib_div::GPvar("comment_dir");
		    if($this->hasAccess($this->mount,$dir,"mkdir")) {
		      $newdir = t3lib_div::GPvar("newdirname");
		      if(strpos($newdir,"/"))
			return $this->pi_wrapInBaseClass("<p class='error'>".$this->pi_getLL("noslashindirs")."</p>");
		      if(preg_match("/[åæøé]/i",$newdir)) {
			return $this->pi_wrapInBaseClass("<p class='error'>".$this->pi_getLL("nospecialchars")."</p>");
		      }
		      if(count(explode("/",$reldir)) > $this->maxDepth-1)
		      	return $this->pi_wrapInBaseClass("<p class='error'>".$this->pi_getLL("todeep")." ".$this->maxDepth."</p>");

		      if(file_exists("$dir/$newdir")) {
		      	return $this->pi_wrapInBaseClass("<p class='error'>Mappen <b>$newdir</b> eksisterer allerede</p>");
		       }
		       if($this->filehandler->func_newfolder(array("data"=>$newdir,target=>"$dir/"))) {
			 if($this->insert_file_entry("DIR",$reldir,$newdir,0,$comment)) {
			   $relative_url = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',array("mountpoint"=>$this->mountID,
												       "dir"=>$reldir));
			   header("Location: http://" . $_SERVER['HTTP_HOST']
				  . $this->div->clean_dir(dirname($_SERVER['PHP_SELF']))
				  . "/" . $relative_url);
			   exit();

			   //			  $content .= "<p class='notice'><b>".$this->pi_getLL("directory")."<br><br>//".$this->mountname."/$insertdir$newdir</b><br><br> ".$this->pi_getLL("createdpress")." <b>".$this->mountname."/$reldir</b> ".$this->pi_getLL("toupdate")."</p>";
			 }
			 else {
			   rmdir("$dir/$newdir");
			   $content .= "<p class='error'>Could not create dir $dir/$newdir, database error.</p><p>$query</p>";

			 }
		       }
		       else {
			 $content .= '<p class="error">'.$this->pi_getLL("error").'</p>';
		       }
		    }
		    else {
		      $content .= '<p class="error">'.$this->pi_getLL("noaccess").'</p>';
		    }
		  }
		  /*
		   *
		   *  TASK = UPLOAD
		   *
		   */
		  if(!empty($uploadfile)) {
		    if(!$this->hasAccess($this->mount,$reldir,"upload")) {
		      return  '<p class="error">'.$this->pi_getLL('no_upload_access').'</p>';
		    }
		    if(1==0) {
		      $content .= '<p class="error">'.$this->pi_getLL('fileextensionerror').': '.$this->conf["allowedExts"].':'.end($str).'</p>';
		    }
		    else {
		      $newname = basename($this->filehandler->func_upload(array("data"=>0,target=>"$dir/")));
		      $size= filesize("$dir/$newname");
		      $comment = t3lib_div::GPvar('comment_file');
		      if($newname) {
					$this->insert_file_entry("FILE",$reldir,$newname,$size,$comment);
					$this->div->log('UPLOAD',"$dir/$newname",$this->mount->uid,filesize("$dir/$newname"));
					$relative_url = $this->pi_getPageLink($GLOBALS['TSFE']->id,'',array("mountpoint"=>$this->mountID,
																											  "dir"=>$reldir));
					header("Location: http://" . $_SERVER['HTTP_HOST']
							 . $this->div->clean_dir(dirname($_SERVER['PHP_SELF']))
			       . "/" . $relative_url);
		      }
		      else {
					$content .= "<p class='error'>".$this->pi_getLL("error")."</p>";
		      }
		    }
		  }
		  /****************************************************
		   *   Render all the mountpoints on the page
		   ************************************************** */
		  foreach($this->mountlist as $mp) {
		    if($mp->uid == $this->mount->uid) {
		      $content .= $this->renderSingleMount($mp,$reldir);
		    }
		    else {
		      $content .= $this->renderSingleMount($mp,"");
		    }
		  }
		  $content .= $this->renderUploadAndCreate($this->mountlist,$reldir);

		}
		/*
		 *
		 *  #######################  UPLOAD_ONLY #######################
		 *
		 */
		elseif($this->mode == "UPLOAD_ONLY")
		  {
		    //
		    // upload the file
		    //
		    if(!empty($uploadfile)) {
		      $newname = basename($this->filehandler->func_upload(array("data"=>0,target=>"$dir/")));
		      $comment = t3lib_div::GPvar('comment_file');
		      if($newname) {
			$this->insert_file_entry("FILE",$reldir,$newname,$comment);
			$content .= "<p class='notice'>".$this->pi_getLL("uploadsuccessful")."</p>";
		      }
		      else {
			$content .= "<p class='error'>".$this->pi_getLL("error")."</p>";
		      }
		    }

		    //
		    // display the upload form
		    //
		    if($this->mount && $this->hasAccess($this->mount,$dir,"upload")) {
		      $content .= "<table width='100%'><tr><td>\n";
		      $typocont["additionalParams"]="&$dir=$reldir&mountpoint=".$this->mount;
		      $content .= "<FORM method=\"POST\" enctype=\"multipart/form-data\" action=\"".$this->cObj->currentPageUrl(array("dir"=>$reldir))."\">";
		      $content .= "<input type='hidden' name='mountpoint' value ='".$mount."'>";
		      $content .= '<input name="uploadfile" type="hidden" value="yes">';
		      $content .= "<span class='command'>".$this->pi_getLL("uploadnew").":</span><br>\n";
		      $content .= '
<input name="upload_0" type="File" class="action">
 <br />'.($this->conf["useDescriptions"] ? '<textarea name="comment" cols="30" rows="5" /></textarea><br />' : '&nbsp; <br/>').'<input type="Submit" value="'.$this->pi_getLL("uploadButton").'" class="actionbutton" />
</form>
 ';
		      $content .= "</td></tr></table>\n";
		    }
		  }
		return $this->pi_wrapInBaseClass($content);

	}
	/**
	 *
	 *
	 *
	 */
	function getFileInfoText($entryname) {
	  //return 'Infotext not implemented for non IE browsers.';
	  $res = $this->dbObj->exec_SELECTquery('size,uploaded,comment,users.name',"tx_mocfilemanager_files as files LEFT JOIN fe_users as users ON files.cruser_id = users.uid","files.file='".$this->dbObj->quoteStr($entryname,"tx_mocfilemanager_files")."'");
	  if($res && $this->dbObj->sql_num_rows($res)) {
	    $row= $this->dbObj->sql_fetch_assoc($res);
	    $desc = $this->pi_getLL('size').': '.($row["size"] != 0 ? t3lib_div::formatSize($row['size']) : $this->pi_getLL('unknown')).' '.$this->pi_getLL('uploaded_date').': '.($row['uploaded'] ? strftime($this->conf['datetimeFormat'],$row['uploaded']) : $this->pi_getLL('unknown')).' '.$this->pi_getLL('uploaded_by').': '.($row['name'] ? $row['name'] : $this->pi_getLL('unknown'));

	    //$desc = "Size: ".($row["size"] != 0 ? t3lib_div::formatSize($row['size']) : $this->pi_getLL('unknown'));
	    return $desc;
	  }
	  else {
	    return "N/A";
	  }
	}
	/**
	 *
	 * Will look up information about all the entries in the array in the DB and create a hidden layer for each item.
	 * The layer will be show when there is a mouse over on the i icon of the file/dir.
	 *
	 *
	 */
	function writeLayers($mount,$parentdir,$entries) {
	  if(!is_array($entries))
	    return "";
	  $index= 0;
	  foreach ($entries as $entry){
	    $row = $this->div->getAdditionalInfo($mount,"$parentdir/$entry");
	    $layerName = "MOC_filemanager_layer_".$index;
	    $content .='
<!-- **** InfoLayer'.$layerName.' **** -->
<div id="'.$layerName.'" width="250" style="position:absolute;visibility: hidden;">
<table width="250" cellspacing="0" class="MainInfoTable">
 <tr>
  <td>
   '.$this->pi_getLL('filename').': '.$entry.'<br />
   '.$this->pi_getLL('size').': '.($row["size"] != 0 ? t3lib_div::formatSize($row['size']) : $this->pi_getLL('unknown')).'<br/>
   '.$this->pi_getLL('uploaded_date').': '.($row['uploaded'] ? strftime($this->conf['datetimeFormat'],$row['uploaded']) : $this->pi_getLL('unknown')).'<br />
   '.$this->pi_getLL('uploaded_by').': '.($row['name'] ? $row['name'] : $this->pi_getLL('unknown')).'<br />
   '.$this->pi_getLL('number_of_downloads').': '.$row['downloads'].'<br />
   '.($this->conf['useDescriptions'] ? '<hr>'.$row["comment"] : '').'
  </td>
 </tr>
</table>
</div>
<!-- ************************* -->

';
	    $index++;
	  }
	  return $content;

	}
	/**
	 *
	 */
	function getAllMountIdsInPages($pidlist) {
	  $res = $this->dbObj->exec_SELECTquery("uid","tx_mocfilemanager_mounts","pid in (".$this->dbObj->cleanIntList($pidlist).")");
	  while($row=$this->dbObj->sql_fetch_assoc($res)) {
	    $list[] = $row["uid"];
	  }
	  if(!is_array($list))
	    return "";
	  return implode(",",$list);
	}

	/**
	 *
	 *  FUNCTION mkdirForm
	 *
	 */
	function mkdirForm($reldir,$mount) {
	  $content .= "<FORM method=\"POST\" enctype=\"multipart/form-data\" action=\"".$this->cObj->currentPageUrl()."\">";
	  $content .= '
<input name="mountpoint" type="hidden" value="'.$this->mount->uid.'">
 <input name="task" type="hidden" value="mkdir">
';
$content .= "<span class='command'>".$this->pi_getLL("createnewdir").":</span><br />\n";
$content .= '
 <input type="hidden" name="dir" value="'.$reldir.'">
 <input name="newdirname" type="text" class="action">
 <br />'.($this->conf["useDescriptions"] ? '<textarea name="comment" cols="20" rows="5" /></textarea><br />' : '&nbsp;<br />').'<input type="Submit" value="'.$this->pi_getLL("createButton").'" class="actionbutton">
</form>
 ';
	  return $content;
	}
	/**
	 *
	 *  FUNCTION hasAccess
	 *
	 *   15/09-03 Sligthly modified by Jan-Erik. Since hte directory mounted is dependent on usergroup,
	 *    everyone will have access as long as they ar logged in.
	 *   4/3-04   Modified so that users can either have read, write. And the mountpoint can now be public.
	 */
	function hasAccess($mount,$dir,$action) {
	  //Now take action depending on type
	  //print ("In hasAccess. Checking $action for mount: ".$mount->getName()."<br />");
	  switch($action) {
	    case "read":
	      if($mount->data["writeperm"] == -2 || $mount->data["mountperm"] == -2) {
		return true;
	      }
	      //Not public mountpoint. Deny if user is not logged in.
	      if(!$GLOBALS["TSFE"]->fe_user->user)
		return false;

	      //We are now sure that the user is logged in. Check if access for all users.
	      if($mount->data["writeperm"] == -1 || $mount->data["mountperm"] == -1) {
		return 1;
	      }
	      //The mount is not public or mountable by all group, so check if the user has read access. Intersect the usergroup with the mountperm(readaccess)
	      if(array_intersect(explode(",",$GLOBALS["TSFE"]->fe_user->user["usergroup"]),explode(",",$mount->data["writeperm"]))) {
		//The user is member of a group that has writeperms. Read is then OK.
		return true;
	      }
	      else {
		//User is not member of a group with write, check if member of a group with read only access
		if(array_intersect(explode(",",$GLOBALS["TSFE"]->fe_user->user["usergroup"]),explode(",",$mount->data["mountperm"]))) {
		  //User is member of read-access group
		  return true;
		}
	      }
	      return false;
	      break;
	  case "mkdir":
	  case "rmdir":
	  case "upload":
	  case "delete":
	  case "rename":
	    if($mount->data["writeperm"] == -2)
	      return true;
	    //Not public mountpoint. Deny if user is not logged in.
	    if(!$GLOBALS["TSFE"]->fe_user->user)
	      return false;
	    //We are now sure that the user is logged in. Check if access for all usergroups.
	    if($mount->data["writeperm"] == -1)
		return true;
	    //All of the above requires write access to the mount.
	    if(array_intersect(explode(",",$GLOBALS["TSFE"]->fe_user->user["usergroup"]),explode(",",$mount->data["writeperm"]))) {
	      return true;
	    }
	    else {
	      return false;
	    }
	    break;
	  default:
	    return false;
	  }

	  return false;
	}
	/* *****************************************************
	 *
	 *  FUNCTION listdirs
	 *
	 *   List all dirs in $active(relative ti $dirname) in a
         *  tree structure starting from $dirname. Calls it self
	 *  recursively.
	 *
         ***************************************************** */
	function listdirs($mountpoint,$dirname,$activeDir,$depth = -1,$islast = array()) {
	  $depth++;
	  $basedirs = t3lib_div::get_dirs($dirname);
	  $param["parameter"]= $this->showFilesPID;
	  if(is_array($basedirs)) {
	    sort($basedirs);
	    $numentries = count($basedirs);
	    $currententry=0;
	    foreach($basedirs as $direntry){
	      $currententry++;
	      $temp ="";
	      for($i=0;$i<$depth;$i++) {
		$temp .= $this->filelistRootLine[$i]."/";
	      }

	      $param["additionalParams"]="&dir=$temp$direntry&mountpoint=$mountpoint";

	      $templast = ($currententry == $numentries) ? 1:0;
	      array_push($islast,$templast);
	      $tempname = ereg_replace("_"," ",$direntry);
	      if($direntry == $this->filelistRootLine[$depth]) {
		$param["ATagParams"]=$this->ActiveATagParams[$depth+1];//'class="filelinkActive"';
		// As explorer
		if($this->asExplorer) {
		  $content .= "<tr><td>";
		  $content .= $this->getPrefix($depth,$islast);
		  $content .= $this->cObj->typolink($tempname,$param);
		  $content .= "</td></tr>";
		}
		else {
		  $content .= $this->cObj->wrap($this->cObj->typolink($tempname,$param),$this->Activewraps[$depth+1]);
		}
		if($depth < $this->maxDepth) {
		  // The recursive call
		  $content .= $this->listdirs($mountpoint,$dirname."/".$direntry,$direntry,$depth,$islast);
		}
	      }
	      else {
		$param["ATagParams"]=$this->ATagParams[$depth+1];//'class="filelink"';
		if($this->asExplorer) {
		  $content .= "<tr><td nowrap>";
		  $content .= $this->getPrefix($depth,$islast);
		  $content .= $this->cObj->typolink($tempname,$param);
		  $content .= "</td></tr>";
		}
		else {
		  $content .= $this->cObj->wrap($this->cObj->typolink($tempname,$param),$this->wraps[$depth+1]);
		}
	      }
	      array_pop($islast);
	    }
	  }
	  else {
	    //return ("<p class='error'>Error in application</p>");
	  }
	  $depth--;
	  return $content;

	}
	function getPrefix($depth,$islast) {
	  $content ="";

	  for($i=0; $i<$depth; $i++) {
	    $image = $islast[$i] ? 'blank.gif':'line.gif';
	    $content .= '<img src="t3lib/gfx/ol/'.$image.'" width="18" height="16" align="top">';
	  }

	  $image = $islast[$depth] ? 'joinbottom.gif':'join.gif';
	  $content .= '<img src="t3lib/gfx/ol/'.$image.'" width="18" height="16" align="top">';
	  //$content .= '<img src="t3lib/gfx/i/sysf.gif" width="18" height="16" align="top">';
	  $content .= '<img src="typo3conf/ext/moc_filemanager/res/folder_small.gif" width="18" height="16" align="top">';
	  return $content;
	}

	/**
	 * Renames a file entry
	 *
	 */
	function rename_file_entry($oldname,$newname,$m_uid,$comment) {
	  $this->dbObj->debugOutput= TRUE;
	  $oldname=$this->div->removeFirstSlash($oldname);
	  $newname=$this->div->removeFirstSlash($newname);
	  $updateArr = array('file'=>$newname);
	  if(trim($comment)) {
	    $updateArr['comment'] = strip_tags($comment);
	  }
	  $this->dbObj->exec_UPDATEquery('tx_mocfilemanager_files','mount = '.intval($m_uid).' AND file="'.$this->dbObj->quoteStr($oldname,'tx_mocfilemanager_files').'"',$updateArr);
	}
	/* *****************************************************
	 *
	 *  FUNCTION insert_file_entry
	 *
	 */
	function insert_file_entry($type,$reldir,$newitem,$size=0,$comment='') {
	  $pid = $this->filesPID;
	  $gid = $GLOBALS["TSFE"]->fe_user->user["usergroup"];
	  if(empty($reldir)) {
	    // JanE hack
	    $insertdir="";
	  }
	  else {
	    $insertdir=$reldir."/";
	  }

	  // Always remove leading AND trailing slashes from filename
	  $insertfile = ereg_replace("^/","",$insertdir.$newitem);
	  $insertfile = ereg_replace("/$","",$insertfile);

	  // Configuration variables
	  if(strtoupper($type) == "DIR") {
	    $ftype=1;
	  }
	  else {
	    $ftype=0;
	  }
	  $ts=time();

	  return  $this->dbObj->exec_INSERTquery('tx_mocfilemanager_files',array('pid'=>$pid,
										 'file'=>$insertfile,
										 'mount'=>$this->mount->uid,
										 'uploaded'=>$ts,
										 'type'=>$ftype,
										 'crdate'=>$ts,
										 'tstamp'=>$ts,
										 'cruser_id'=>$GLOBALS["TSFE"]->fe_user->user["uid"],
										 'comment' => htmlspecialchars($comment),
										 'size'=>$size,
									 ));

	  //$res = mysql(TYPO3_db,$query);
	  if($res)
	    return true;
	  return false;
	}
	/**
	 * remove_file_entry
	 *
	 *
	 */
	function remove_file_entry($reldir) {

	  $deletefile = ereg_replace("^/","",$reldir);
	  //	  $query = "DELETE from tx_mocfilemanager_files WHERE file ='$deletefile' AND mount = '".$this->mount->uid."' AND pid='".$this->filesPID."'";
	  $where = "file ='".$this->dbObj->quoteStr($deletefile,'tx_mocfilemanager_files')."' AND mount = '".$this->mount->uid."' AND pid='".$this->filesPID."'";
//	  print	  $this->dbObj->DELETEquery('tx_mocfilemanager_files',$where);//mysql(TYPO3_db,$query);
	  $res = $this->dbObj->exec_DELETEquery('tx_mocfilemanager_files',$where);//mysql(TYPO3_db,$query);


	  if($res)
	    return true;
	  return false;

	}
	/**
	 * Get localized label with variable substitution.
	 *   %1 - first parameter
	 *   %2 - second parameter
	 *   %3 - third parameter
	 */
	function getLL_params($key, $param1="", $param2="", $param3="")
	{
		$xlate = $this->pi_getLL($key);
		if ($param1)
		{
			$xlate = str_replace("%1", $param1, $xlate);
		}
		if ($param2)
		{
			$xlate = str_replace("%2", $param2, $xlate);
		}
		if ($param3)
		{
			$xlate = str_replace("%3", $param3, $xlate);
		}
		return $xlate;
	}
	/**
	 * RendersingleMount
	 *
	 *
	 */
	function renderSingleMount($mount,$reldir) {
		$dir=$this->div->clean_dir($mount->getDir()."/".$reldir);
		/*
		 *
		 * SHOW Files
		 *
		 */
		$dirs = array();
		$files = array();
		$icons = array("doc"=>"doc.gif",
							"xls"=>"xls.gif",
							"html"=>"html1.gif",
							"txt"=>"txt.gif",
							"gz" => "zip.gif",
							"zip" => "zip.gif",
							"tar" => "zip.gif",
							"jpg" => "jpg.gif",
							"gif" => "gif.gif",
							"bmp" => "bmp.gif",
							"pdf" => "pdf.gif",
							"png" => "png.gif",
							"php" => "php3.gif",
							"php3" => "php3.gif");
		$typoconf = array();
		$typoconf["parameter"] = $GLOBALS["TSFE"]->id;
		if(!$this->hasAccess($mount,"$dir","read")) {
			return "";
			//	    return $this->pi_wrapInBaseClass('<p class="error">'.$this->pi_getLL("noaccess").'</p>');
		}
		if (is_dir("$dir")) {
			$files = t3lib_div::getFilesInDir("$dir","",0,"mtime");
			$numfiles = count($files);
			if($this->canUseLayers) {
				$content .= '
<script language="JavaScript">
activeInfoLayerHandle = null;
function MOC_filemanager_activateInfo(id) {
 //alert(id);
 MOC_filemanager_hideActiveInfo();
 handle = document.getElementById(id);

 posx = event.clientX + document.body.scrollLeft-250;
 posy = event.clientY + document.body.scrollTop;

 handle.style.top= posy;
 handle.style.left = posx;

 handle.style.visibility = "visible";
 activeInfoLayerHandle = handle;
}
function MOC_filemanager_hideActiveInfo() {
 if(activeInfoLayerHandle) {
   activeInfoLayerHandle.style.visibility = "hidden";

 }
 activeInfoLayerHandle = null;
}
</script>
';
			}

			$content .= "<table id='contenttable' width='100%' border='0' cellspacing='0' cellpadding='0'>";
			$nicereldir = ereg_replace("_"," ",$reldir);
			$content .= '<tr><td colspan="4" valign="center" class="borderbottom">';

			// if we are in a subdirectory, then display the "folder_up" icon with a link to the parent directory

			// calculate the Parent Directory
			$parentdir = "";
			if ($reldir && $this->conf["showParentDir"])
	      {
				// find the last slash in the relative dir
				$pos = strrpos($reldir, "/");
				if ($pos === false)
				{
					// not found - the "newdir" is at the root of the mount
					$parentdir = "";
				}
				else
				{
					$parentdir = substr($reldir, 0, $pos);
					$parentdir = str_replace(" ", "%20", $parentdir);
				}
	      }
			if ($reldir && $this->conf["showParentDir"] && !$this->conf["parentDirStyle"])
	      {
				// parentDirStyle is false - substitute the regular directory icon with the Up icon
				$typoconf["additionalParams"]="&dir=$parentdir&mountpoint=".$mount->uid;
				$content .= $this->cObj->typolink("<img src=\"typo3conf/ext/moc_filemanager/res/folder_up.gif\" border=\"0\" alt=\"".$this->pi_getLL("up")."\">",$typoconf);
	      }
			else
	      {
				$content .= '<img src="typo3conf/ext/moc_filemanager/res/folder.gif">';
	      }
			$content .= '<span class="dirpath">'.$mount->getName().'://'.$nicereldir.'</span></td></tr>';
			if(!empty($conf["ShowFiles."]["ATagParams"])) {
				$typoconf["ATagParams"] = $conf["ShowFiles."]["ATagParams"];
			}
			else{
				$typoconf["ATagParams"] = 'class="filelink"';
			}

			$dirs=  t3lib_div::get_dirs("$dir");
			// if showParentDir is set and parentDirStyle is true, add in the Up folder icon in the list of regular directories
			if ($reldir && $this->conf["showParentDir"] && $this->conf["parentDirStyle"]) {
				$content .="<tr><td>";
				$numDirs=count($dirs);
				$img = (($numfiles == 0) && ($numDirs==0)) ? "joinbottom-large.gif" : "joinlarge.gif";
				$content .= '<img src="typo3conf/ext/moc_filemanager/res/'.$img.'" align="top">';
				$typoconf["additionalParams"]="&dir=$parentdir&mountpoint=".$mount->uid;
				$content .= $this->cObj->typolink("<img src=\"typo3conf/ext/moc_filemanager/res/folder_up.gif\" border=\"0\" alt=\"".$this->pi_getLL("up")."\">",$typoconf);
				$content .= $this->cObj->typolink($this->pi_getLL("up"),$typoconf);
				$content .="</td><td colspan='3' style='text-align: right;'>&nbsp;";
				$content .= "</td></tr>";
			}
			$index=0;
			if(is_array($dirs)) {
				sort($dirs);
				$currentDir = 0;
				$numDirs=count($dirs);
				foreach($dirs as $direntry) {
					$info = $this->div->getAdditionalInfo($mount,"$reldir/$direntry");
					$layerName = "MOC_filemanager_layer_".$index;
					$currentDir++;
					$cleandirentry = ereg_replace("_"," ",$direntry);
					$content .="<tr><td>";
					$img = (($numfiles == 0) && ($currentDir==$numDirs)) ? "joinbottom-large.gif" : "joinlarge.gif";
					$content .= '<img src="typo3conf/ext/moc_filemanager/res/'.$img.'" align="top">';
					$content .= '<img src="typo3conf/ext/moc_filemanager/res/folder.gif">';
					$typoconf["additionalParams"]="&dir=$reldir/$direntry&mountpoint=".$mount->uid;
					$content .= $this->cObj->typolink("$cleandirentry",$typoconf);
					$content .="</td>";
					$content .= '<td>&nbsp;</td>';
					$content .= '<td>'.date(" d M Y.",filemtime("$dir/$file")).'</td>';
					$content .= "<td style='text-align: right;'>";
/*
					if($this->canUseLayers) {
						$content .= '<img src="t3lib/gfx/zoom2.gif" border="no" onMouseOver="MOC_filemanager_activateInfo(\''.$layerName.'\');" onMouseOut="MOC_filemanager_hideActiveInfo();" />';
					}
					else {
						$content .= '<img src="t3lib/gfx/zoom2.gif" border="no" title="'.$this->getFileInfoText("$reldir/$direntry").'"/>';
					}
*/
					if($this->hasAccess($mount,"$dir","rmdir")) {
						$typoconf["additionalParams"]="&dir=$reldir&task=delete&filename=$direntry&mountpoint=".$mount->uid;
						$content .= $this->cObj->typolink("<img alt='".$this->pi_getLL("delete")."' border=0 src=\"typo3conf/ext/moc_filemanager/res/delete.gif\">",$typoconf);
						$typoconf["additionalParams"]="&dir=$reldir&oldname=$direntry&task=rename&mountpoint=".$mount->uid;
						$content .=$this->cObj->typolink("<img src=\"t3lib/gfx/rename.gif\" border=\"0\" alt='".$this->pi_getLL("rename")."'>",$typoconf);
					}
					$content .= "</td></tr>";
					$index++;
				}
			}
			if(is_array($files)){
				$count = 0;
				foreach($files as $file){
					$info = $this->div->getAdditionalInfo($mount,"$reldir/$file");
					$layerName = "MOC_filemanager_layer_".$index;
					//$content .="<tr><td><span class='filelink'>";
					$content .="<tr><td>";
					$count++;
					$str = explode(".",$file); // Break it in pieces.
					$extfile = $icons[end($str)];
					if(empty($extfile))
						$extfile="default.gif";
					// **** The Jan-Erik Way
					$typoconf["additionalParams"]="&filename=$file&dir=$reldir&task=download&mountpoint=".$mount->uid;
					$image = ($count == $numfiles) ? 'joinbottom-large.gif':'joinlarge.gif';
					$content .= '<img src="typo3conf/ext/moc_filemanager/res/'.$image.'" align="top">';
					$imgpath = $this->conf["useOwnIcons"] ? "typo3conf/ext/moc_filemanager/res/fileicons/" : "t3lib/gfx/fileicons/";
					$content .= $this->cObj->typoLink("<img src=\"$imgpath$extfile\" border=0 >$file",$typoconf);
//					$content .= "<span class='info'>".date (" d M Y.", filemtime("$dir/$file"))."</span>";
					$content .= '<td>'.t3lib_div::formatSize($info['size'],"b | Kb| Mb| Gb").'</td>';
					$content .= '<td>'.date(" d M Y.",filemtime("$dir/$file")).'</td>';
					$content .="</td><td style='text-align: right;'>";
/*
					if($this->canUseLayers) {
						$content .= '<img src="t3lib/gfx/zoom2.gif" border="no" onMouseOver="MOC_filemanager_activateInfo(\''.$layerName.'\');" onMouseOut="MOC_filemanager_hideActiveInfo();" />';
					}
					else {
						$content .= '<img src="t3lib/gfx/zoom2.gif" border="no" title="'.$this->getFileInfoText("$reldir/$direntry").'"/>';
					}
*/
					if($this->hasAccess($mount,$dir,"delete")) {
						$typoconf["additionalParams"]="&filename=$file&dir=$reldir&task=delete&mountpoint=".$mount->uid;
						$content .=$this->cObj->typolink("<img alt='".$this->pi_getLL("delete")."' border=0 src=\"typo3conf/ext/moc_filemanager/res/delete.gif\">",$typoconf);
					}
					if($this->hasAccess($mount,$dir,"upload")) {
					 $typoconf["additionalParams"]="&dir=$reldir&oldname=".t3lib_div::rawUrlEncodeFP($file)."&dir=$reldir&task=rename&mountpoint=".$mount->uid;
					 $content .=$this->cObj->typolink("<img src=\"t3lib/gfx/rename.gif\" border=\"0\" alt='".$this->pi_getLL("rename")."'>",$typoconf);
					}
					$content .= "</div></td></tr>";
					$index++;
				}
	    }
			$content .="</table>";
			//Need to check that we are working in Internet explorer...
			if($this->canUseLayers) {
				$content .= $this->writeLayers($mount,$reldir,array_merge($dirs,$files));
			}
		}
		else {
			$content .= "<p class='error'>".$mount->getDir()."/$reldir ".$this->pi_getLL("doesnotexist")."</p>";
		}
		return $content;
	}
	/**
	 *
	 */
	function renderSingleLine($type,$dir,$file,$index) {

		return $content;
	}
	/**
	 *
	 */
  function initFilehandler() {
    $this->filehandler = new t3lib_extFileFunctions();
    $f_ext = array("ftpspace"=>array("allow"=>$this->conf["allowedExts"]));

    $this->filehandler->init($this->filehandler_mps,$f_ext);
    $this->filehandler->init_actionPerms(31);
  }
  /**
   * Renders the upload and create (No TemplaVoila)
   */
  function renderUploadAndCreate($mountlist,$reldir) {
    foreach($mountlist as $mount) {
      $dir=$this->div->clean_dir($mount->getDir()."/".$reldir);
      if($this->hasAccess($mount,$dir,"upload")) {
	$targetlist[] = '<option value="'.$mount->uid.'" '.($mount->uid == $this->mountID ? "SELECTED" : "").'>'.$mount->getName().'</option>';
      }
    }
    if(count($targetlist) > 0) {
      $content .= "<FORM method=\"POST\" enctype=\"multipart/form-data\" action=\"".$this->cObj->currentPageUrl(array("dir"=>$reldir))."\">";
      $content .= "<table width='100%'><tr><td valign=\"top\">\n";
      $content .= "<span class='command'>".$this->pi_getLL("uploadnew").":</span><br>\n";
      $content .= '
<input name="upload_0" type="File" class="action" />
 <br />'.($this->conf["useDescriptions"] ? '<textarea name="comment_file" cols="30" rows="5" /></textarea><br />' : '&nbsp; <br/>').'<input type="Submit" value="'.$this->pi_getLL("uploadButton").'" class="actionbutton" name="uploadfile"/>';
      $content .= '</td><td valign="top">';

      //Render the create dir form (if allowed)
      if(!$this->conf['disableMkdir']) {
	$content .= "<span class='command'>".$this->pi_getLL("createnewdir").":</span><br />\n";
	$content .= '
 <input name="newdirname" type="text" class="action" />
 <br />'.($this->conf["useDescriptions"] ? '<textarea name="comment_dir" cols="20" rows="5" /></textarea><br />' : '&nbsp;<br />').'<input type="Submit" value="'.$this->pi_getLL("createButton").'" class="actionbutton" name="mkdir">
 ';
	$content .= "</td></tr>";
      }
      $content .= "<tr><td colspan='2'>";
      //if(count($targetlist) == 1) {
      if($this->selectFrom == "PIVARS") {
	$content .= "<input type='hidden' name='mountpoint' value ='".$mountlist[0]->uid."'>";
	}
      else {
	$content .= $this->pi_getLL("target_mountpoint").": <select name='mountpoint'>";
	$content .= implode("\n",$targetlist);
	$content .= "</select>";
      }
      $content .="</td></tr></table></form>";
    }
    return $content;
  }
  /**
   * Init a single Mountpoint
   */
  function initSingleMountPoint($mountpoint) {
    /*
     * Perform common initialization required by both VIEW & UPLOAD_ONLY
     */

    if(!$mountpoint->uid) {
      //return $this->pi_wrapInBaseClass("<p class='error'>Could not find mountpoint with UID: ".$mountpoint->uid."</p>");
      return -1;
    }

    // ************ Init the filehandler (TYPO3 extFilefunctions) *********

    //Clean up filepath
    $this->filehandler_mps[$mountpoint->uid]["path"] = $this->div->clean_dir($this->documentRoot."/".$mountpoint->data["path"])."/";
    $this->filehandler_mps[$mountpoitn->uid]["name"] = $mountpoint->data["name"];
  }
  /**
   *
   */
  function transferFlexToData() {
    $this->cObj->data['field_mode'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'field_mode');
    $this->cObj->data['field_selectfrom'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'field_selectfrom');
    $this->cObj->data['field_from'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'field_from');
  }



}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/moc_filemanager/pi1/class.tx_mocfilemanager_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/moc_filemanager/pi1/class.tx_mocfilemanager_pi1.php"]);
}

?>