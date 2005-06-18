<?Php
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
 * Plugin 'Directory menu' for the 'moc_filemanager' extension.
 *
 * @author	Jan-Erik Revsbech <jer@moccompany.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once(t3lib_extMgm::extPath("moc_filemanager")."/res/class.mount.php");
require_once(t3lib_extMgm::extPath("moc_filemanager")."/res/class.filemanager_div.php");

class tx_mocfilemanager_pi2 extends tslib_pibase {
	var $prefixId = "tx_mocfilemanager_pi2";		// Same as class name
	var $scriptRelPath = "pi2/class.tx_mocfilemanager_pi2.php";	// Path to this script relative to the extension dir.
	var $extKey = "moc_filemanager";	// The extension key.

	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
	  $this->conf=$conf;
	  $this->pi_setPiVarDefaults();
	  $this->pi_loadLL();

	  $this->dbObj = $GLOBALS['TYPO3_DB'];
	      $this->dbObj->debugOutput = true;
	  $this->div = new filemanager_div();
	  $this->showPage = ($this->conf["ShowFilesPID"]) ? $this->cObj->stdWrap($this->conf["ShowFilesPID"],$this->conf["ShowFilesPID."]) : $GLOBALS["TSFE"]->id;

	  $this->userGroups = 	$GLOBALS["TSFE"]->fe_user->user["usergroup"];

	  $this->mountsPID = $this->cObj->stdWrap($this->conf["mountsPID"],$this->conf["mountsPID."]);
	  $this->filesPID = $this->cObj->stdWrap($this->conf["filesStoragePID"],$this->conf["filesStoragePID."]);
	  if(intval($this->filesPID) == 0) {
		  $this->filesPID = $this->mountsPID;
	  }
	  $this->div->FilesStoragePID = $this->filesPID;
	  $this->div->MountsStoragePID = $this->mountsPID;

	  if(!($this->mountsPID)) {
	    //$this->mountsPID = $GLOBALS["TSFE"]->id;
	    $this->mountsPID=0;
	  }
	  $this->mountsPID = $this->pi_getPidList($this->mountsPID,250);



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
	  $usergroups = explode(",",$this->userGroups);
	  $count = 0;
	  $whereClause = "p.hidden = 0 AND p.deleted = 0 AND ( m.mountperm=-1 OR m.writeperm=-1";
	  foreach ($usergroups as $ug) {
	    //	    if($count != 0)
	    $whereClause .= " OR ";
	    $count++;
	    $whereClause .= "FIND_IN_SET('".intval($ug)."',m.mountperm) OR FIND_IN_SET('".intval($ug)."',m.writeperm)";
	  }
	  $whereClause .= ")";
	  $res = $this->dbObj->exec_SELECTquery('p.title,f.uid,type,file,(UNIX_TIMESTAMP(NOW())-f.uploaded) as age,f.uploaded,mount,m.pid,m.name,path','tx_mocfilemanager_files as f,tx_mocfilemanager_mounts as m,pages as p','p.uid=m.pid AND f.mount=m.uid AND f.type="0" AND '.$whereClause,'','age',$this->conf["limitNewest"]+3);

//	  $res = $this->dbObj->exec_SELECTquery('f.uid,type,file,(UNIX_TIMESTAMP(NOW())-f.uploaded) as age,f.uploaded,mount,m.pid,m.name,path','tx_mocfilemanager_files as f,tx_mocfilemanager_mounts as m','f.mount=m.uid AND f.type="0" AND '.$whereClause,'','age',$this->conf["limitNewest"]+3);
	  $content .= '<h3>Seneste filer</h3>';
	  $content .= '<table class="filelist" width="100%" cellspacing="0" cellpadding="0" cellmargin="0">';
//	  $content .= '<tr><th>File</th><th>Age</th><th>Uploaded by</th><th>Size</th></tr>';
	  $content .= '<tr class="fileheader" ><th>Fil</th><th>Alder</th><th>Uploaded af</th><th>St&oslash;rrelse</th></tr>';

	  $documentRoot = $this->div->clean_dir($this->cObj->stdWrap($this->conf["documentRoot"],$this->conf["documentRoot."]));
	  $this->documentRoot = $documentRoot;
	  $showedFiles=0;
	  $this->mounts = array();
	  while(($row = $this->dbObj->sql_fetch_assoc($res)) && ($showedFiles < $this->conf["limitNewest"])) {
		  if(file_exists($this->div->clean_dir($documentRoot."/".$row["path"])."/".$row["file"])) {
			  if(!$this->mounts[$row["mount"]]) {
				  $this->mounts[$row["mount"]] = new mount(intval($row["mount"]),$this->documentRoot);
			  }
			  $showedFiles++;
			  $file = $row["file"];
			  $temp = t3lib_div::split_fileref ($row["file"]);
			  $extfile = $icons[$temp["fileext"]];
			  if(empty($extfile))
				  $extfile="default.gif";
			  // **** The Jan-Erik Way
//			  $typoconf["parameter"]= $row["pid"];
			  $typoconf["parameter"]= $this->showPage;
			  $filename = $temp["file"];
			  $reldir = $this->div->clean_dir($temp["path"]);
			  if($reldir=="/")
				  $reldir = "";
			  $fileinfo = $this->div->getAdditionalInfo($this->mounts[$row["mount"]],$reldir."/".$filename);
			  $typoconf["additionalParams"]="&filename=$filename&dir=$reldir&task=download&mountpoint=".$row["mount"];
			  $imgpath = $this->conf["useOwnIcons"] ? "typo3conf/ext/moc_filemanager/res/fileicons/" : "t3lib/gfx/fileicons/";
//			  $filename = $row["title"]."/".$row["name"]."/".$reldir."/".$temp["file"];
//			  $filename = $row["name"]."/".$reldir."/".$temp["file"];
			  $filename = $temp["file"];
			  $content .= "<tr><td><img src=\"$imgpath$extfile\" width=\"16\" border=0 >".$this->cObj->typolink($filename,$typoconf)."</td>\n";
			  //	      $content .= '<tr><td>'.$this->cObj->typolink($filename,$typoconf).'</td>';
			  $content .= '<td>'.$this->cObj->calcAge($row["age"],$this->pi_getLL("ageformat")).'</td>';
			  $content .= '<td>'.($fileinfo["name"] ? $fileinfo["name"] : "BvHD").'</td>';
			  $content .= '<td style="text-align: right;">'.($fileinfo["size"] ? t3lib_div::formatSize($fileinfo["size"],'k|Kb|Mb|Gb') : "Ukendt").'</td>';
		  }
		  else {
			  //File does not exist. Remove entry from DB.
			  $this->dbObj->exec_deleteQuery("tx_mocfilemanager_files","uid=".$row["uid"]);
	    }
	  }
	  $content .= '</table>';
	  return $this->pi_wrapInBaseClass($content);
	}
	/**
	 * Lookup up additional information for files in the DB.
	 *
	 *
	 */
	function getAdditionalInfo($file,$mount) {
	  $file=$this->div->removeFirstSlash($file);
	  $res = $this->dbObj->exec_SELECTquery("comment,uploaded,size,users.name","tx_mocfilemanager_files as files LEFT JOIN fe_users as users ON files.cruser_id=users.uid","file='$file' AND mount='$mount'");
	  if($res && $this->dbObj->sql_num_rows($res)) {
	    $row = $this->dbObj->sql_fetch_assoc($res);
	    $this->dbObj->sql_free_result($res);
	  }
	  return $row;
	}

}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/moc_filemanager/pi2/class.tx_mocfilemanager_pi2.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/moc_filemanager/pi2/class.tx_mocfilemanager_pi2.php"]);
}

?>
