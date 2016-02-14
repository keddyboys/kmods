<?php
function download_subcats($id, $type="") {
    global $locale, $subcats_available;
 
	$result = dbquery("SELECT download_cat_id, download_cat_name, download_cat_description FROM ".DB_DOWNLOAD_CATS." WHERE ".groupaccess('download_cat_access')." AND download_cat_parent='".$id."' ORDER BY download_cat_name");
	$k = dbrows($result);
	if ($k > 0) {
		if ($type == "2") {
			$counter = 0; $columns = 2;
			echo "<tr>\n";
			echo "<td class='tbl2' colspan='".$columns."'><span class='side'><strong>".$locale['432']."</strong></span></td><tr>\n";
			while ($data = dbarray($result)) {
				if ($counter != 0 && ($counter % $columns == 0)) { echo "</tr>\n<tr>\n"; }
				$num = dbcount("(download_cat)", DB_DOWNLOADS, "download_cat='".(int)$data['download_cat_id']."'");
				if($num > 0) $subcats_available = true;
				echo "<td valign='top' width='50%' class='tbl1 download_idx_cat_name'><!--download_idx_cat_name--><a href='".FUSION_SELF."?cat_id=".$data['download_cat_id']."'>".$data['download_cat_name']."</a> <span class='small2'>($num)</span>";
				if ($data['download_cat_description'] != "") { echo "<br />\n<span class='small'>".$data['download_cat_description']."</span>"; }
				echo "</td>\n";
				$counter++;
			}
			if ($counter % $columns != 0) { echo "<td valign='top' width='50%' class='tbl1 download_idx_cat_name'></td>\n"; }
			echo "<div style='margin:5px'></div>";
			echo "</tr>\n";
		} else {
			echo "<br />";	
			echo "<tr><td colspan='8' class='tbl1 small' style='text-align:left;'>";
			echo "<span class='side'><strong>".$locale['432'].": </strong></span>";
			while ($data = dbarray($result)) {
				$k--;
				$num = dbcount("(download_cat)", DB_DOWNLOADS, "download_cat='".(int)$data['download_cat_id']."'");
				echo "<a href='".FUSION_SELF."?cat_id=".$data['download_cat_id']."'>".$data['download_cat_name']."</a> <span class='small2'>(".$num.")</span>";
				if ($k > 0) echo  ", ";
				
			}
			echo "</td></tr>\n";
		}
	}
}
function download_subdownloads($id){

	$result = dbquery("SELECT download_cat_id, download_cat_name FROM ".DB_DOWNLOAD_CATS." WHERE download_cat_parent='".(int)$id."' ORDER BY download_cat_name");
	$sub_list  = "";
	
	if (dbrows($result)) {
		
		$k = 1;
		while ($data = dbarray($result)) {
			if (isset($_GET['action']) && $_GET['action'] == "edit") { 
			$sel = ($_GET['download_cat_id'] == $data['download_cat_id'] ? " selected='selected'" : ""); 
			} else {
			$sel = (isset($_GET['cat_id']) && $_GET['cat_id'] == $data['download_cat_id'] ? " selected='selected'" : "");
		    }
			$sub_list .= "<option value='".$data['download_cat_id']."'".$sel.">-".$data['download_cat_name']."</option>";
			$k++;
		}
	}
	return $sub_list;
}

function download_admin_subcats($id) {
	global $aidlink, $locale;$sublist = "";
		$result = dbquery("SELECT download_cat_id, download_cat_name FROM ".DB_DOWNLOAD_CATS." WHERE download_cat_parent='".(int)$id."' ORDER BY download_cat_name");
	if (dbrows($result)) {
		
		while ($data = dbarray($result)) {
			if (!isset($_GET['download_cat_id']) || !isnum($_GET['download_cat_id'])) { $_GET['download_cat_id'] = 0; }
			if ($data['download_cat_id'] == $_GET['download_cat_id']) { $p_img = "off"; $div = ""; } else { $p_img = "on"; $div = "style='display:none'"; }
			$sublist .= "<tr>\n";
			$sublist .= "<td class='tbl2'>-".$data['download_cat_name']."</td>\n";
			$sublist .= "<td class='tbl2' style='text-align:right;'><img src='".get_image("panel_$p_img")."' name='b_".$data['download_cat_id']."' alt='' onclick=\"javascript:flipBox('".$data['download_cat_id']."')\" /></td>\n";
			$sublist .= "</tr>\n";
			$result2 = dbquery("SELECT download_id, download_title, download_url, download_file FROM ".DB_DOWNLOADS." WHERE download_cat='".$data['download_cat_id']."' ORDER BY download_title");
			if (dbrows($result2) != 0) {
				$sublist .= "<tr>\n<td colspan='2'>\n";
				$sublist .= "<div id='box_".$data['download_cat_id']."'".$div.">\n";
				$sublist .= "<table cellpadding='0' cellspacing='0' style='width:100%;'>\n";
				while ($data2 = dbarray($result2)) {
					if (!empty($data2['download_file']) && file_exists(DOWNLOADS.$data2['download_file'])) {
						$download_url = DOWNLOADS.$data2['download_file'];
					} elseif (!strstr($data2['download_url'],"http://") && !strstr($data2['download_url'],"../")) {
						$download_url = BASEDIR.$data2['download_url'];
					} else {
						$download_url = $data2['download_url'];
					}
					$sublist .= "<tr>\n<td class='tbl1'><a href='".$download_url."' target='_blank'>".$data2['download_title']."</a></td>\n";
					$sublist .= "<td class='tbl1' style='text-align:right;width:100px;'><a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;download_cat_id=".$data['download_cat_id']."&amp;download_id=".$data2['download_id']."'>".$locale['442']."</a> -\n";
					$sublist .= "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;download_cat_id=".$data['download_cat_id']."&amp;download_id=".$data2['download_id']."' onclick=\"return confirm('".$locale['460']."');\">".$locale['443']."</a></td>\n";
					$sublist .= "</tr>\n";
				}
				$sublist .= "</table>\n</div>\n</td>\n</tr>\n";
			}
		}
	}
	return $sublist;
}
function download_admin_sublist($id) {
	global $aidlink, $locale, $i;
	$sublist = "";
$result = dbquery("SELECT download_cat_id, download_cat_name, download_cat_description, download_cat_access FROM ".DB_DOWNLOAD_CATS." WHERE download_cat_parent='".(int)$id."' ORDER BY download_cat_name");
	if (dbrows($result) != 0) {
		 
		while ($data = dbarray($result)) {
			$cell_color = ($i % 2 == 0 ? "tbl1" : "tbl2");
			$sublist .= "<tr>\n";
			$sublist .= "<td class='".$cell_color."'>-".$data['download_cat_name']."\n";
			if ($data['download_cat_description']) { $sublist .= "<br /><span class='small'>".trimlink($data['download_cat_description'], 45)."</span>"; }
			$sublist .= "</td>\n<td align='center' width='1%' class='$cell_color' style='white-space:nowrap'>".getgroupname($data['download_cat_access'])."</td>\n";
			$sublist .= "<td align='center' width='1%' class='$cell_color' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;cat_id=".$data['download_cat_id']."'>".$locale['443']."</a> -\n";
			$sublist .= "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;cat_id=".$data['download_cat_id']."' onclick=\"return confirm('".$locale['450']."');\">".$locale['444']."</a></td>\n";
			$sublist .= "</tr>\n";
			echo download_admin_subcats($data['download_cat_id']);// subdownloads
		}
	}
	
	return $sublist;
}
function download_admin_editlist($id) {
	global $data;
$editlist = ""; $sel = "";
	$result2 = dbquery("SELECT download_cat_id, download_cat_name FROM ".DB_DOWNLOAD_CATS."  WHERE download_cat_parent='0' ORDER BY download_cat_name");
	$checkparent = dbcount("(download_cat_id)", DB_DOWNLOAD_CATS, "download_cat_parent='".(int)$id."'");
	if (dbrows($result2) != 0) {
	        $editlist .= "<option value='0'".$sel."></option>\n";
		while ($data2 = dbarray($result2)) {
			if (isset($_GET['action']) && $_GET['action'] == "edit") { $sel = ($data['download_cat_parent'] == $data2['download_cat_id'] ? " selected='selected'" : ""); }
			if ((isset($_GET['action']) && $_GET['action'] == "edit") &&  ($_GET['cat_id'] != $data2['download_cat_id']) && ($checkparent == 0)) {
			    $editlist .= "<option value='".$data2['download_cat_id']."'$sel>".$data2['download_cat_name']."</option>\n";
			} else {
			    $editlist .= "<option value='".$data2['download_cat_id']."'$sel>".$data2['download_cat_name']."</option>\n";   	
			}	
		}
	}
	
     return $editlist;
}
?>