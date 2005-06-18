<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_mocfilemanager_files"] = Array (
	"ctrl" => $TCA["tx_mocfilemanager_files"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "file,uploaded,type,mount,size,comment,downloads"
	),
	"feInterface" => $TCA["tx_mocfilemanager_files"]["feInterface"],
	"columns" => Array (
		"file" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.file",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"uploaded" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.uploaded",		
			"config" => Array (
				"type" => "input",
				"size" => "12",
				"max" => "20",
				"eval" => "datetime",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"type" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.type",		
			"config" => Array (
				"type" => "radio",
				"items" => Array (
					Array("LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.type.I.0", "0"),
					Array("LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.type.I.1", "1"),
				),
			)
		),
		"mount" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.mount",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_mocfilemanager_mounts",	
				//				"foreign_table_where" => "AND tx_mocfilemanager_mounts.pid=###STORAGE_PID### ORDER BY tx_mocfilemanager_mounts.uid",	
				"foreign_table_where" => " ORDER BY tx_mocfilemanager_mounts.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
				)
			),
		"size" => Array (
				 "exclude" => 1,
				 "label" =>  "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.mount",
				 "config" => Array (
						    "type" => "input",
						    "size" => "30"
						    )
				 ),
		"comment" => Array (
				    "exclude" => 1,
				    "label" =>  "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.mount",
				    "config" => Array (
						       "type" => "text",
						       "size" => "30"
						       )
				    ),
		"downloads" => Array (
				 "exclude" => 1,
				 "label" =>  "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_files.downloads",
				 "config" => Array (
						    "type" => "input",
						    "size" => "30"
						    )
				 ),
		
	),
	"types" => Array (
		"0" => Array("showitem" => "file;;;;1-1-1, uploaded, type, mount,comment,downloads")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

$TCA["tx_mocfilemanager_mounts"] = Array (
	"ctrl" => $TCA["tx_mocfilemanager_mounts"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "path,view,mountperm,name,text"
	),
	"feInterface" => $TCA["tx_mocfilemanager_mounts"]["feInterface"],
	"columns" => Array (
		"path" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_mounts.path",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"mountperm" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_mounts.mountperm",
			"config" => Array (
					   "type" => "select",
					   "foreign_table" => "fe_groups",
					   "items" => array(array("All groups",-1),
							    array("Public",-2)),							    
					   "size" => 5,
					   "minitems" => 0,
					   "maxitems" => 100,
					   ),
		),
		"writeperm" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_mounts.writeperm",		
			"config" => Array (
					   "type" => "select",
					   "foreign_table" => "fe_groups",
					   "items" => array(array("All groups",-1),
							    array("Public",-2)),
					   "size" => 5,
					   "minitems" => 0,
					   "maxitems" => 100,
					   ),
		),
		"name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:moc_filemanager/locallang_db.php:tx_mocfilemanager_mounts.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
	),
	"types" => Array (
			  //		"0" => Array("showitem" => "path;;;;1-1-1, public, view, mountperm, writeperm, name, text;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_mocfilemanager/rte/]")
		"0" => Array("showitem" => "name,path;;;;1-1-1, mountperm, writeperm")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>