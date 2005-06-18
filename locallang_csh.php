<?php
$LOCAL_LANG = Array (
		     'default' => Array ('path.description'=>'Relative path for this mountpoint',
					 'path.details'=>'The path on the server to store files on. The path is relative to the constant documentRoot. This constant can be set with the constant editor and is usually configured by the admin of the site',
					 'mountperm.description'=>'Groups allowed to mount the mountpoint',
					 'mountperm.details'=>'All usergroups in this list will be allowed to mount the mountpoint and hence have read access. Select public if all users (no matter i they are logged in or not) are allowed to acces the mountpoint. Select All groups to make all FE users access the mount.',
					 'writeperm.description'=>'Groups allowed to write to the mountpoint',
					 'writeperm.details'=>'List of all the usergroups that will be allowed to upload and create subdirectories in this mountpoint. If a usergroups is listed here, it will always have mountpermission, even though it is not listed in the mountperm list! Select public to make this mount writeable by all even if they are not logged in. NOT recomended. This will allows everyone to write to your webserver!',
					 'name.description'=>'The name of the mountpoint',
					 'name.details'=>'The name that the mountpoint will have in the list of mountpoints.',
					 )
		     );
		     //					 'view.seeAlso' => 'tt_content:list_type,tt_news_cat',
?>