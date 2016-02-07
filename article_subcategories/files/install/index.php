<?php
require_once "../maincore.php";
require_once THEMES."templates/header.php";
if (file_exists(BASEDIR."install/locale/".$settings['locale'].".php")) {
	include BASEDIR."install/locale/".$settings['locale'].".php";
} else {
	include BASEDIR."install/locale/English.php";
}
if (iADMIN && (iUSER_RIGHTS != "" || iUSER_RIGHTS != "C")) {


    opentable($locale['install_101']);
	        echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
            echo "<td class='tbl2'>".$locale['install_102']."</td>\n";
       $sf_result = dbquery("SHOW COLUMNS FROM ".DB_FORUMS." LIKE 'forum_parent'");
	   $sd_result = dbquery("SHOW COLUMNS FROM ".DB_DOWNLOAD_CATS." LIKE 'download_cat_parent'");
	   $sa_result = dbquery("SHOW COLUMNS FROM ".DB_ARTICLE_CATS." LIKE 'article_cat_parent'");
	        $sf_val = ((dbrows($sf_result) == 0) ? "install" : "delete");
			$sd_val = ((dbrows($sd_result) == 0) ? "install" : "delete");
			$sa_val = ((dbrows($sa_result) == 0) ? "install" : "delete");
		    echo "</td>\n</tr>\n<tr>\n";
     		echo "<td class='tbl2'>".$locale['sf_101']."</td>\n";
		    echo "<td class='tbl2'>\n<form name='subcats' method='post' action='".BASEDIR."install/install_subforums.php'>";
            echo "<input type='hidden' name='action' value='".$sf_val."'>";
            echo "<input type='submit' name='".$sf_val."' value='".((dbrows($sf_result) == 0) ? $locale['install_105'] : $locale['install_106'])."' class='button'></form>";
			echo "</td>\n</tr>\n<tr>\n";
		    echo "<td class='tbl2'>\n".$locale['sd_101']."</td>\n";
		    echo "<td class='tbl2'>\n<form name='subcats' method='post' action='".BASEDIR."install/install_subdownloads.php'>";
            echo "<input type='hidden' name='action' value='".$sd_val."'>";
            echo "<input type='submit' name='".$sd_val."' value='".((dbrows($sd_result) == 0) ? $locale['install_105'] : $locale['install_106'])."' class='button'></form></div>";
		    echo "</td>\n</tr>\n<tr>\n";
     		echo "<td class='tbl2'>".$locale['sa_101']."</td>\n";
		    echo "<td class='tbl2'>\n<form name='subcats' method='post' action='".BASEDIR."install/install_subforums.php'>";
            echo "<input type='hidden' name='action' value='".$sa_val."'>";
            echo "<input type='submit' name='".$sa_val."' value='".((dbrows($sa_result) == 0) ? $locale['install_105'] : $locale['install_106'])."' class='button'></form>";
			echo "</td>\n</tr>\n</table>\n";
			
	closetable();
} else {
opentable("".$locale['sd_101']."");
echo "<div style='text-align:center'>\n<br />".$locale['install_104']."<br />\n<br />\n</div>\n";
closetable();
}
require_once THEMES."templates/footer.php";
?>