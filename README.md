Subcategories for forums v1.30 Mod for PHP-Fusion v7.02.07

What it does:
This MOD enables sub categories for forums. You can create sub categories of current categories. A forum can have both subforums and threads.

Installation:
1.Upload folder install in the root folder.
2.Run http://www.yoursite.tld/install/index.php
3.Upon successful installation delete install folder.
You must do this before uploading or modifying files to prevent error warning!
4.Upload files to the folders using the folder structure in the download

Core File Modifications:
1.
- administration/forums.php,
- forum/index.php, 
- forum/viewthread.php, 
- forum/viewforum.php, 
- forum/options.php, 
- locale/English/admin/forums.php, 
- locale/English/forum/main.php
2.forum_parent field added to DB_FORUMS table.
3.Added includes/subcats_include.php

Uninstallation:
1.Upload folder install in the root folder.
2.Run http://www.yoursite.tld/install/index.php
3.Upon successful uninstallation delete install folder.
4.Delete includes/subcats_include.php.
5.Put the original core files back. 

 Web:  http://www.kmods.ro
 Support:  http://dev.kmods.ro  Version: 1.30 

Notes: Back up all relevant files being installing this mod.
   
Changelog:

 v1.00 - First public test version [BETA]
 v1.10 - Bugs fixed
 v1.20 - Bugs fixed
 v1.21 - Forum access fixed. (slaughter)
 v1.22 - Jump to forum fixed, move to forum fixed, new_folder fixed 
 v1.23 - Updated to 7.02.03 standards 
 v1.30 - Updated to 7.02.07 standards 
