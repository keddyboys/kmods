<?php
require_once "../maincore.php";
require_once THEMES."templates/header.php";
if (file_exists(BASEDIR."install/locale/".$settings['locale'].".php")) {
	include BASEDIR."install/locale/".$settings['locale'].".php";
} else {
	include BASEDIR."install/locale/English.php";
}

if (iADMIN && (iUSER_RIGHTS != "" || iUSER_RIGHTS != "C")) {

		if (!isset($_GET['install_ok']) && !isset($_GET['delete_ok'])) {
		opentable($locale['install_01']);
		$result = dbquery("SHOW COLUMNS FROM ".DB_DOWNLOAD_CATS." LIKE 'download_cat_parent'");
		if (dbrows($result) == 0) {
		        echo "<div style='text-align:center'>".$locale['install_02'];
                echo "<br /><br /><center>
                <form name='subforums' method='post' action='".FUSION_SELF."'>
                <input type='hidden' name='action' value='install'>
                <input type='submit' name='install' value='".$locale['install_03']."' class='button'></form></div></center>";
		} else {
		        echo "<br /><div style='text-align:center'>".$locale['install_04'];
                echo "<br /><br /><center>
                <form name='subforums' method='post' action='".FUSION_SELF."'>
                <input type='hidden' name='action' value='delete'>
                <input type='submit' name='delete' value='".$locale['install_05']."' class='button' onClick='return DeleteItem()'></form></div></center>";
                echo "<script type='text/javascript'>
               function DeleteItem()
            {
               return confirm('".$locale['install_06']."');
            }
               </script>\n";
        }
		closetable();
    }
            if (isset($_POST['action']) && $_POST['action'] == "install") {
                if (isset($_POST['install'])) {
				// Alter table
		        $result = dbquery("ALTER TABLE ".DB_DOWNLOAD_CATS." ADD download_cat_parent MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0' AFTER download_cat_sorting");
				redirect(FUSION_SELF."?install_ok"); die;
				}	
			}	
			if (isset($_POST['action']) && $_POST['action'] == "delete") {
                if (isset($_POST['delete'])) {
				// Alter table
		        $result = dbquery("ALTER TABLE ".DB_DOWNLOAD_CATS." DROP download_cat_parent");
				redirect(FUSION_SELF."?delete_ok"); die;
				}	
			}	
				
				
            if (isset($_GET['install_ok'])){
            opentable("".$locale['install_07']."");
            echo "<div style='text-align:center'>\n<br />".$locale['install_08']."<br />\n<br />\n</div>\n";
            echo "<div style='text-align:center'><a href='".BASEDIR."index.php'>".$locale['install_11']."</a></div>";
            closetable();
        }

            if (isset($_GET['delete_ok'])){
            opentable("".$locale['install_07']."");
            echo "<div style='text-align:center'>\n<br />".$locale['install_09']."<br />\n<br />\n</div>\n";
            echo "<div style='text-align:center'><a href='".BASEDIR."index.php'>".$locale['install_11']."</a></div>";
            closetable();
        }
		
} else {
opentable("".$locale['install_01']."");
echo "<div style='text-align:center'>\n<br />".$locale['install_10']."<br />\n<br />\n</div>\n";
closetable();
}

require_once THEMES."templates/footer.php";

?>
