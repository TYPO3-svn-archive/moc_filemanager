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
 */

class mount {
  var $uid;
  var $pid;
  var $data;
  var $dbObj;
  var $basedir;
  function mount($ID,$basedir) {
    $this->uid=$ID;
    $this->basedir = $basedir;
    $this->dbObj = $GLOBALS['TYPO3_DB'];
    //    $res = $this->dbObj->exec_SELECTquery('*','tx_mocfilemanager_mounts','pid in ('.$this->dbObj->cleanIntList($PID).') AND uid='.intval($ID));
    $res = $this->dbObj->exec_SELECTquery('*','tx_mocfilemanager_mounts', 'uid='.intval($ID));
    if($res) {
      $this->data = $this->dbObj->sql_fetch_assoc($res);
      $this->dbObj->sql_free_result($res);
    }
    $this->pid = $this->data["pid"];
  }
  function isView() {
    return $this->data["view"];
  }
  function isPublic() {
    return $this->data["public"];
  }
  function getName() {
    return $this->data["name"];
  }
  /* *****************************************************
   *
   *  FUNCTION clear_dir
   * 
   */
  function clean_dir($thedir) {
    return ereg_replace("[\/\. ]*$","",$thedir);		// Removes all dots, slashes and spaces after a path...
  }
  function getDir() {
   // Cleaning up the path, so its nice and tidy:)
    return $this->clean_dir($this->basedir."/".$this->data["path"]);
  }
  function getRelDir() {
    return $this->clean_dir($this->data["path"]);
  }

}