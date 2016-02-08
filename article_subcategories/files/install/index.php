<?php
require_once "../maincore.php";
require_once THEMES."templates/header.php";
if (file_exists(BASEDIR."install/locale/".$settings['locale'].".php")) {
	include BASEDIR."install/locale/".$settings['locale'].".php";
} else {
	include BASEDIR."install/locale/English.php";
}
    if (file_exists(BASEDIR."install/install_subarticles.php")) {
            redirect(BASEDIR."install/install_subarticles.php");
    } else {
            opentable($locale['install_101']);
			echo "<div style='text-align:center'>\n<br />".$locale['install_112']."<br />\n<br />\n</div>\n";
            echo "<div style='text-align:center'><a href='".BASEDIR."index.php'>".$locale['install_107']."</a></div>";
            closetable();
    }
require_once THEMES."templates/footer.php";
?>