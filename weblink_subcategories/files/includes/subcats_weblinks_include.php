<?php

function weblink_subcats($id, $type="") {
    global $locale;
 
	$result = dbquery("SELECT weblink_cat_id, weblink_cat_name, weblink_cat_description FROM ".DB_WEBLINK_CATS." WHERE ".groupaccess('weblink_cat_access')." AND weblink_cat_parent='".(int)$id."'  ORDER BY weblink_cat_name");
	$k = dbrows($result);
	if ($k > 0) {
		if ($type == "2") {
			$counter = 0; $columns = 2; 
			echo "<span class='side'><strong>".$locale['413']."</strong></span><br />\n";
			echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
			while ($data = dbarray($result)) {
				if ($counter != 0 && ($counter % $columns == 0)) { echo "</tr>\n<tr>\n"; }
				$num = dbcount("(weblink_cat)", DB_WEBLINKS, "weblink_cat='".(int)$data['weblink_cat_id']."'");
				echo "<td valign='top' width='50%' class='tbl'><a href='".FUSION_SELF."?cat_id=".$data['weblink_cat_id']."'>".$data['weblink_cat_name']."</a> <span class='small2'>(".$num.")</span>";
				if ($data['weblink_cat_description'] != "") { echo "<br />\n<span class='small'>".$data['weblink_cat_description']."</span>"; }
				echo "</td>\n";
				$counter++;
			}
			if ($counter % $columns != 0) { echo "<td valign='top' width='50%' class='tbl'></td>\n"; }
			echo "</tr>\n</table>\n";
			echo "<div style='margin:5px'></div>";
			echo "<br />";
		} else {
			echo "<br />";
			echo "<span class='side'><strong>".$locale['413'].": </strong></span>";
			while ($data = dbarray($result)) {
				$k--;
				$num = dbcount("(weblink_cat)", DB_WEBLINKS, "weblink_cat='".(int)$data['weblink_cat_id']."'");
				echo "<a href='".FUSION_SELF."?cat_id=".$data['weblink_cat_id']."'>".$data['weblink_cat_name']."</a> <span class='small2'>(".$num.")</span>";
				if ($k > 0) echo  ", ";
			}
		}
	}
}

function weblink_admin_subsubcats($id) {
	global $aidlink, $locale;
	$result = dbquery("SELECT weblink_cat_id, weblink_cat_name FROM ".DB_WEBLINK_CATS." WHERE weblink_cat_parent='".(int)$id."' ORDER BY weblink_cat_name");
	$sublist = "";
	while ($data = dbarray($result)) {
		if (!isset($_GET['weblink_cat_id']) || !isnum($_GET['weblink_cat_id'])) { $_GET['weblink_cat_id'] = 0; }
		if ($data['weblink_cat_id'] == $_GET['weblink_cat_id']) { $p_img = "off"; $div = ""; } else { $p_img = "on"; $div = "style='display:none'"; }
		$sublist .= "<tr>\n";
		$sublist .= "<td class='tbl2'>--".$data['weblink_cat_name']."</td>\n";
		$sublist .= "<td class='tbl2' align='right'><img src='".get_image("panel_$p_img")."' alt='' name='b_".$data['weblink_cat_id']."' onclick=\"javascript:flipBox('".$data['weblink_cat_id']."')\" /></td>\n";
		$sublist .= "</tr>\n";
		$result2 = dbquery("SELECT weblink_id, weblink_name, weblink_url FROM ".DB_WEBLINKS." WHERE weblink_cat='".$data['weblink_cat_id']."' ORDER BY weblink_name");
		if (dbrows($result2)) {
			$sublist .= "<tr>\n<td colspan='2'>\n";
			$sublist .= "<div id='box_".$data['weblink_cat_id']."'".$div.">\n";
			$sublist .= "<table cellpadding='0' cellspacing='0' width='100%'>\n";
			while ($data2 = dbarray($result2)) {
				$sublist .= "<tr>\n";
				$sublist .= "<td class='tbl'><a href='".$data2['weblink_url']."' target='_blank'>".$data2['weblink_name']."</a></td>\n";
				$sublist .= "<td width='75' class='tbl'><a href='".FUSION_SELF.$aidlink."&action=edit&weblink_cat_id=".$data['weblink_cat_id']."&weblink_id=".$data2['weblink_id']."'>".$locale['533']."</a> -\n";
				$sublist .= "<a href='".FUSION_SELF.$aidlink."&action=delete&weblink_cat_id=".$data['weblink_cat_id']."&weblink_id=".$data2['weblink_id']."' onclick=\"return confirm('".$locale['550']."');\">".$locale['534']."</a></td>\n";
				$sublist .= "</tr>\n";
			}
			$sublist .= "</table>\n</div>\n</td>\n</tr>\n";
		} else {
			$sublist .= "<tr>\n<td colspan='2'>\n";
			$sublist .= "<div id='box_".$data['weblink_cat_id']."' style='display:none'>\n";
			$sublist .= "<table width='100%' cellspacing='0' cellpadding='0'>\n<tr>\n";
			$sublist .= "<td class='tbl'>".$locale['535']."</td>\n";
			$sublist .= "</tr>\n</table>\n</div>\n";
		}
			
			$sublist .= weblink_admin_subsubcats($data['weblink_cat_id']); 
		}
		$sublist .= "</tr>\n";
	
	return $sublist;
}

function weblink_admin_subweblinks($id){

	$result = dbquery("SELECT weblink_cat_id, weblink_cat_name FROM ".DB_WEBLINK_CATS." WHERE weblink_cat_parent='".(int)$id."' ORDER BY weblink_cat_name");
	$sub_list  = "";
	
	if (dbrows($result)) {
		
		$k = 1;
		while ($data = dbarray($result)) {
			if (isset($_GET['action']) && $_GET['action'] == "edit") { 
			$sel = ($_GET['weblink_cat_id'] == $data['weblink_cat_id'] ? " selected='selected'" : ""); 
			} else {
			$sel = (isset($_GET['cat_id']) && $_GET['cat_id'] == $data['weblink_cat_id'] ? " selected='selected'" : "");
		    }
			$sub_list .= "<option value='".$data['weblink_cat_id']."'".$sel.">-".$data['weblink_cat_name']."</option>";
			$k++;
		}
	}
	return $sub_list;
}

function weblink_admin_subcats($id) {
global $aidlink, $locale, $cell_color;
	$sublist = "";
	$result2 = dbquery("SELECT weblink_cat_id, weblink_cat_name, weblink_cat_description, weblink_cat_access FROM ".DB_WEBLINK_CATS." WHERE weblink_cat_parent='".(int)$id."' ORDER BY weblink_cat_name");
	while ($data2 = dbarray($result2)) {
		$sublist .= "<tr>\n";
		$sublist .= "<td class='$cell_color'>-".$data2['weblink_cat_name']."\n";
		$data2['weblink_cat_description'] ? $sublist .= "<br />\n<span class='small'>".trimlink($data2['weblink_cat_description'], 45)."</span>" : "";
		$sublist .= "</td>\n";
		$sublist .= "<td align='center' width='1%' class='$cell_color' style='white-space:nowrap'>".getgroupname($data2['weblink_cat_access'])."</td>\n";
		$sublist .= "<td align='center' width='1%' class='$cell_color' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&action=edit&cat_id=".$data2['weblink_cat_id']."'>".$locale['533']."</a> -\n";
		$sublist .= "<a href='".FUSION_SELF.$aidlink."&action=delete&cat_id=".$data2['weblink_cat_id']."' onclick=\"return confirm('".$locale['440']."');\">".$locale['534']."</a></td>\n";
		$sublist .= "</tr>\n";
	}
	return $sublist;
}

function weblink_admin_editlist($id) {
	global $data;
	$editlist = ""; $sel = "";
	$checkparent = dbcount("(weblink_cat_id)", DB_WEBLINK_CATS, "weblink_cat_parent='".(int)$id."'");
	$result2 = dbquery("SELECT weblink_cat_id, weblink_cat_name FROM ".DB_WEBLINK_CATS."  WHERE weblink_cat_parent='0' ORDER BY weblink_cat_name");
	if (dbrows($result2) != 0) {
	        $editlist .= "<option value='0'".$sel."></option>\n";
		while ($data2 = dbarray($result2)) {
			if (isset($_GET['action']) && $_GET['action'] == "edit") { $sel = ($data['weblink_cat_parent'] == $data2['weblink_cat_id'] ? " selected='selected'" : ""); }
			
			if ((isset($_GET['action']) && $_GET['action'] == "edit") &&  ($_GET['cat_id'] != $data2['weblink_cat_id']) && ($checkparent == 0)) {
			    $editlist .= "<option value='".$data2['weblink_cat_id']."'$sel>".$data2['weblink_cat_name']."</option>\n";
			} else {
			    $editlist .= "<option value='".$data2['weblink_cat_id']."'$sel>".$data2['weblink_cat_name']."</option>\n";   	
			}	
		}
	}
        return $editlist;
}
?>