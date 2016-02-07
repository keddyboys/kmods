<?php
require_once "../maincore.php";
require_once THEMES."templates/header.php";
if (file_exists(BASEDIR."install/locale/".$settings['locale'].".php")) {
	include BASEDIR."install/locale/".$settings['locale'].".php";
} else {
	include BASEDIR."install/locale/English.php";
}

if (iADMIN && (iUSER_RIGHTS != "" || iUSER_RIGHTS != "C")) {

            if (isset($_POST['action']) && $_POST['action'] == "install") {
                if (isset($_POST['install'])) {
				// Alter table
		        $result = dbquery("ALTER TABLE ".DB_FORUMS." ADD forum_parent MEDIUMINT( 8 ) NOT NULL DEFAULT '0' AFTER forum_cat");
				redirect(FUSION_SELF."?install_ok"); die;
				}	
			}	
			if (isset($_POST['action']) && $_POST['action'] == "delete") {
                if (isset($_POST['delete'])) {
				// Alter table
		        $result = dbquery("ALTER TABLE ".DB_FORUMS." DROP forum_parent");
				redirect(FUSION_SELF."?delete_ok"); die;
				}	
			}	
				
				
           if (isset($_GET['install_ok'])){
            opentable("".$locale['install_108']."");
            echo "<div style='text-align:center'>\n<br />".$locale['install_109']."<br />\n<br />\n</div>\n";
            echo "<div style='text-align:center'><a href='".BASEDIR."install/index.php'>".$locale['install_107']."</a></div>";
            closetable();
        }

            if (isset($_GET['delete_ok'])){
            opentable("".$locale['install_108']."");
            echo "<div style='text-align:center'>\n<br />".$locale['install_110']."<br />\n<br />\n</div>\n";
            echo "<div style='text-align:center'><a href='".BASEDIR."install/index.php'>".$locale['install_107']."</a></div>";
            closetable();
        }
		
} else {
opentable("".$locale['sf_101']."");
echo "<div style='text-align:center'>\n<br />".$locale['install_10']."<br />\n<br />\n</div>\n";
closetable();
}

require_once THEMES."templates/footer.php";

?>