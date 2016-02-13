<?php
function photo_subcats($id) {
global $aidlink, $settings, $locale;
	$rows = dbcount("(album_id)", DB_PHOTO_ALBUMS, "album_parent='".(int)$id."'");
    $sublist = "";
	if ($rows > 0) {
	include LOCALE.LOCALESET."admin/photoalbums.php";
	opentable($locale['404']);
		if (!isset($_GET['rowstart']) || isset($_GET['rowstart']) && !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
			$result = dbquery(
			"SELECT ta.album_id, ta.album_title, ta.album_thumb, ta.album_order, ta.album_datestamp, ta.album_parent, ta.album_access,
			tu.user_id, tu.user_name, tu.user_status FROM ".DB_PHOTO_ALBUMS." ta
			LEFT JOIN ".DB_USERS." tu ON ta.album_user=tu.user_id
			WHERE album_parent='".$_GET['album_id']."'
			ORDER BY album_order LIMIT ".$_GET['rowstart'].",".$settings['thumbs_per_page']
		);
		$counter = 0; $k = ($_GET['rowstart'] == 0 ? 1 : $_GET['rowstart'] + 1);
		echo "<table cellpadding='0' cellspacing='1' width='100%'>\n<tr>\n";
		if ($rows > $settings['thumbs_per_page']) {
			echo "<div align='center' styl='margin-top:5px;'>\n".makepagenav(true,$_GET['rowstart'], $settings['thumbs_per_page'], $rows, 3, FUSION_SELF.$aidlink."&")."\n</div>\n"; } // Pimped
		while ($data = dbarray($result)) {
			$up = ""; $down = "";
			$parent = ($data['album_parent'] == 0 ? "" : "&parent=".$data['album_parent']);
			if ($rows != 1){
				$order = $data['album_order'] - 1;
				$order = $data['album_order'] + 1;
				if ($k == 1){
					$down = " ·\n<a href='".FUSION_SELF.$aidlink."&page=".$_GET['rowstart']."&action=down&order=$order&album_id=".$data['album_id']."$parent><img src='".get_image("right")."' alt='".$locale['467']."' title='".$locale['468']."' style='border:0px;vertical-align:middle' /></a>\n";
				}elseif ($k < $rows){
					$up = "<a href='".FUSION_SELF.$aidlink."&page=".$_GET['rowstart']."&action=up&order=$order&album_id=".$data['album_id']."$parent'><img src='".get_image("left")."' alt='".$locale['467']."' title='".$locale['466']."' style='border:0px;vertical-align:middle' /></a> ·\n";
					$down = " ·\n<a href='".FUSION_SELF.$aidlink."&page=".$_GET['rowstart']."&action=down&order=$order&album_id=".$data['album_id']."$parent><img src='".get_image("right")."' alt='".$locale['467']."' title='".$locale['468']."' style='border:0px;vertical-align:middle' /></a>\n";
				} else {
					$up = "<a href='".FUSION_SELF.$aidlink."&page=".$_GET['rowstart']."&action=up&order=$order&album_id=".$data['album_id']."$parent'><img src='".get_image("left")."' alt='".$locale['467']."' title='".$locale['466']."' style='border:0px;vertical-align:middle' /></a> ·\n";
				}
			}
			if ($counter != 0 && ($counter % $settings['thumbs_per_row'] == 0)) { echo "</tr>\n<tr>\n"; }
			echo "<td align='center' valign='top' class='tbl'>\n";
			echo "<strong>".$data['album_title']."</strong><br /><br />\n<a href='photos.php".$aidlink."&album_id=".$data['album_id']."'>";
			if ($data['album_thumb'] && file_exists(PHOTOS.$data['album_thumb'])){
				echo "<img src='".PHOTOS.rawurlencode($data['album_thumb'])."' alt='".$locale['460']."' style='border:0px' />";
			} else {
				echo $locale['461'];
			}
			echo "</a><br /><br />\n<span class='small'>".$up;
			echo "<a href='photoalbums.php".$aidlink."&action=edit&album_id=".$data['album_id']."$parent'>".$locale['468']."</a> ·\n";
			echo "<a href='photoalbums.php".$aidlink."&action=delete&album_id=".$data['album_id']."$parent' onclick=\"return PhotosWarning\">".$locale['469']."</a> ".$down;
			echo "<br /><br />\n".$locale['462'].showdate("shortdate", $data['album_datestamp'])."<br />\n";
			echo $locale['463'].profile_link($data['user_id'], $data['user_name'], $data['user_status'])."<br />\n";
			echo $locale['464'].getgroupname($data['album_access'])."<br />\n";
			echo $locale['465'].dbcount("(photo_id)", DB_PHOTOS, "album_id='".$data['album_id']."'")."</span><br />\n";
			echo "</td>\n";
			$counter++; $k++;
		}
		echo "</tr>\n<tr>\n<td align='center' colspan='".$settings['thumbs_per_row']."' class='tbl2'><a href='photoalbums.php".$aidlink."&action=refresh'>".$locale['470']."</a></td>\n</tr>\n</table>\n";
		if ($rows > $settings['thumbs_per_page']) {
			echo "<div align='center' style='margin-top:5px;'>\n".makepagenav(true,$_GET['rowstart'], $settings['thumbs_per_page'], $rows, 3, FUSION_SELF.$aidlink."&")."\n</div>\n"; }
	closetable();
	} 
	//echo "<script type='text/javascript'>\n"."function PhotosWarning(value) {\n";
	//echo "return confirm ('".$locale['500']."');\n}\n</script>";

    return $sublist;
}

function photo_admin_editopts($id) {
	global $data;
	    
	$editlist = ""; $sel = "";		
	$checkparent = dbcount("(album_id)", DB_PHOTO_ALBUMS, "album_parent='".(int)$id."'");
    $result2 = dbquery("SELECT album_id, album_title FROM ".DB_PHOTO_ALBUMS."  WHERE album_parent='0' ORDER BY album_order");
	if (dbrows($result2) != 0) {
	        $editlist .= "<option value='0'".$sel."></option>\n";
		while ($data2 = dbarray($result2)) {
			if (isset($_GET['action']) && $_GET['action'] == "edit") { $sel = ($data['album_parent'] == $data2['album_id'] ? " selected='selected'" : ""); }
			if ((isset($_GET['action']) && $_GET['action'] == "edit") && ($_GET['album_id'] != $data2['album_id']) && ($checkparent == 0)) {
			$editlist .= "<option value='".$data2['album_id']."'$sel>".$data2['album_title']."</option>\n";
			} elseif (!isset($_GET['action'])) {	
			$editlist .= "<option value='".$data2['album_id']."'$sel>".$data2['album_title']."</option>\n";
            }			
				
		}		
	
    }
    return $editlist;
}
function photo_subalbums($id) {	
global $settings, $locale;
   $list = "";
	$subs = dbcount("(album_id)", DB_PHOTO_ALBUMS, groupaccess('album_access')." AND album_parent='".(int)$id."'");
	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
	if ($subs > 0) {
	opentable($locale['407']);
		$s_result = dbquery(
			"SELECT ta.album_id, album_title, album_thumb, album_datestamp, tu.user_id,user_name FROM ".DB_PHOTO_ALBUMS." ta
			LEFT JOIN ".DB_USERS." tu ON ta.album_user=tu.user_id
			WHERE ".groupaccess('album_access')." AND album_parent='".(int)$id."' ORDER BY album_order
			LIMIT ".(int)$_GET['rowstart'].",".(int)$settings['thumbs_per_page']
		);
		$counter = 0; $r = 0; $k = 1;
		if ($subs > $settings['thumbs_per_page']) { echo "<div align='center' style='margin-top:5px;'>\n".makepagenav($_GET['rowstart'], $settings['thumbs_per_page'], $rows, 3)."\n</div>\n"; }
		echo "<table cellpadding='0' cellspacing='1' width='100%'>\n<tr>\n";
		while ($s_data = dbarray($s_result)) {
			if ($counter != 0 && ($counter % $settings['thumbs_per_row'] == 0)) { echo "</tr>\n<tr>\n"; }
			echo "<td align='center' valign='top' class='tbl'>\n";
			echo "<strong>".$s_data['album_title']."</strong><br /><br />\n<a href='".FUSION_SELF."?album_id=".$s_data['album_id']."'>";
			if ($s_data['album_thumb'] && file_exists(PHOTOS.$s_data['album_thumb'])){
			echo "<img src='".PHOTOS.$s_data['album_thumb']."' alt='".$s_data['album_thumb']."' title='".$locale['401']."' style='border:0px' />";
			} else {
				echo "<img src='".PHOTOS."nophoto.jpg' alt='' title='".$locale['401']."' style='border:0px' />";
			}
			echo "</a><br /><br />\n<span class='small'>\n";
			echo $locale['405'].dbcount("(photo_id)", DB_PHOTOS, "album_id='".$s_data['album_id']."'")."</span><br />\n";
			echo "</td>\n";
			$counter++; $k++;
		}
		echo "</tr>\n</table>\n";
		closetable();
		if ($subs > $settings['thumbs_per_page']) { echo "<div align='center' style='margin-top:5px;'>\n".makepagenav($_GET['rowstart'], $settings['thumbs_per_page'], $rows, 3)."\n</div>\n"; }
    }
      return $list;
	  
}	  	
?>