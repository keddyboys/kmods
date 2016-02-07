<?php
//forum subcategories functions begin
function forum_admin_refreshs($parent){
	$result2 = dbquery("SELECT forum_id FROM ".DB_FORUMS." WHERE forum_parent='".$parent."' ORDER BY forum_order");
	$k = 1;
	$list = "";
	while ($data2 = dbarray($result2)) {
		$result3 = dbquery("UPDATE ".DB_FORUMS." SET forum_order='".$k."' WHERE forum_id='".$data2['forum_id']."'");
		$k++;
		$list .= forum_admin_refreshs($data2['forum_id']);
	}
	return $list;
}

function forum_admin_parent($data2, $result2, $k){
	
	global $aidlink, $locale;
	
	$forumR = "";
	$forumR .= "<tr>\n";
	$forumR .= "<td class='tbl1'><span class='alt'>";
	$forumR .= ($data2['forum_parent'] == 0 ) ? $data2['forum_name'] : "--".$data2['forum_name'];
	$forumR .= "</span>\n";
	$forumR .= "[<a href='".FUSION_SELF.$aidlink."&action=prune&forum_id=".$data2['forum_id']."'>".$locale['563']."</a>]<br />\n";
	$forumR .= ($data2['forum_description'] ? "<span class='small'>".$data2['forum_description']."</span>" : "")."</td>\n";
	$forumR .= "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$data2['forum_order']."</td>\n";
	$forumR .= "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>\n";
	$parent = ($data2['forum_parent'] == 0 ? "" : "&parent=".$data2['forum_parent']);
	
	if (dbrows($result2) != 1) {
		$up = $data2['forum_order'] - 1; $down = $data2['forum_order'] + 1;
		if ($k == 1) {
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&action=md&order=$down&forum_id=".$data2['forum_id']."&t=forum&cat=".$data2['forum_cat']."$parent'><img src='".get_image("down")."' alt='".$locale['557']."' title='".$locale['557']."' style='border:0px;' /></a>\n";
		} elseif ($k < dbrows($result2)) {
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&action=mu&order=$up&forum_id=".$data2['forum_id']."&t=forum&cat=".$data2['forum_cat']."$parent'><img src='".get_image("up")."' alt='".$locale['556']."' title='".$locale['558']."' style='border:0px;' /></a>\n";
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&action=md&order=$down&forum_id=".$data2['forum_id']."&t=forum&cat=".$data2['forum_cat']."$parent'><img src='".get_image("down")."' alt='".$locale['557']."' title='".$locale['557']."' style='border:0px;' /></a>\n";
		} else {
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&action=mu&order=$up&forum_id=".$data2['forum_id']."&t=forum&cat=".$data2['forum_cat']."$parent'><img src='".get_image("up")."' alt='".$locale['556']."' title='".$locale['558']."' style='border:0px;' /></a>\n";
		}
	}
	$forumR .= "</td>\n";
	$forumR .= "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&action=edit&forum_id=".$data2['forum_id']."&t=forum'>".$locale['554']."</a> ::\n";
	$forumR .= "<a href='".FUSION_SELF.$aidlink."&action=delete&forum_id=".$data2['forum_id']."&t=forum$parent' onclick=\"return confirm('".$locale['570']."');\">".$locale['555']."</a></td>\n";
	$forumR .= "</tr>\n";
	
	return $forumR;
}

function forum_admin_subparent($parent){

	$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_parent='$parent' ORDER BY forum_order asc");
	$forumR = "";
	
	if (dbrows($result)) {
		
		$k = 1;
		while ($data = dbarray($result)) {
			
			$forumR .= forum_admin_parent($data, $result, $k);
			$k++;
			$forumR .= forum_admin_subparent($data['forum_id']);
			
		}
		
	}
	return $forumR;
}

function forum_subcats($forum_id) {
global $settings, $locale, $userdata, $lastvisited;
$a_result = dbquery("SELECT * FROM ".DB_FORUMS." f LEFT JOIN ".DB_USERS." u on f.forum_lastuser=u.user_id WHERE ".groupaccess('f.forum_access')." AND forum_parent='".$_GET['forum_id']."' ORDER BY forum_order");
if (dbrows($a_result) != 0 ) {

echo "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border forum_idx_table'>\n<tr>\n";
echo "<td colspan='2' class='tbl2'>".$locale['401']."</td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$locale['402']."</td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$locale['403']."</td>\n";
echo "<td width='1%' class='tbl2' style='white-space:nowrap'>".$locale['404']."</td>\n";
echo "</tr>\n";

while ($a_data = dbarray($a_result)) {	
echo "<tr>\n";
$moderators = "";
		if ($a_data['forum_moderators']) {
			$mod_groups = explode(".", $a_data['forum_moderators']);
			foreach ($mod_groups as $mod_group) {
				if ($moderators) $moderators .= ", ";
				$moderators .= $mod_group<101 ? "<a href='".BASEDIR."profile.php?group_id=".$mod_group."'>".getgroupname($mod_group)."</a>" : getgroupname($mod_group);
			}
		}
		if ($a_data['forum_lastpost'] > $lastvisited) {
		$forum_match = "\|" . $a_data ['forum_lastpost'] . "\|" . $a_data ['forum_id'];
			if (iMEMBER && ($a_data['forum_lastuser'] == $userdata['user_id'] || preg_match("({$forum_match}\.|{$forum_match}$)", $userdata['user_threads']))) {
				$fim = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
			} else {
				$fim = "<img src='".get_image("foldernew")."' alt='".$locale['560']."' />";
			}
		} else {
			$fim = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
		}

		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$fim</td>\n";
		echo "<td class='tbl1 forum_name'><!--forum_name--><a href='viewforum.php?forum_id=".$a_data['forum_id']."'>".$a_data['forum_name']."</a><br />\n";
		if ($a_data['forum_description'] || $moderators) {
			echo "<span class='small'>".$a_data['forum_description'].($a_data['forum_description'] && $moderators ? "<br />\n" : "");
			echo ($moderators ? "<strong>".$locale['411']."</strong>".$moderators."</span>\n" : "</span>\n")."\n";
		}
		echo "</td>\n";
		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$a_data['forum_threadcount']."</td>\n";
		echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>".$a_data['forum_postcount']."</td>\n";
		echo "<td width='1%' class='tbl2' style='white-space:nowrap'>";
		if ($a_data['forum_lastpost'] == 0) {
			echo $locale['405']."</td>\n</tr>\n";
		} else {
			echo showdate("forumdate", $a_data['forum_lastpost'])."<br />\n";
			echo "<span class='small'>".$locale['406'].profile_link($a_data['forum_lastuser'], $a_data['user_name'], $a_data['user_status'])."</span></td>\n";
			echo "</tr>\n";
			}
		}	
echo "</table>";
    }
}

function forum_jump_to($forum_id){
	global $fdata;
	$jump_list = "";$sel = "";
	$result = dbquery("SELECT f.forum_id, f.forum_parent, f2.forum_name AS forum_cat_name FROM ".DB_FORUMS." f
	LEFT JOIN ".DB_FORUMS." f2 ON f2.forum_id=f.forum_id
	WHERE ".groupaccess('f.forum_access')." AND f2.forum_parent='$forum_id'");
	while($data = dbarray($result)){
	$sel = ($data['forum_id'] == $fdata['forum_id'] ? " selected='selected'" : "");  
		$jump_list .= "<option value='".$data['forum_id']."'$sel>  -".$data['forum_cat_name']."</option>\n";
	}
	return $jump_list;
}
function forum_move_to($forum_id){
	$move_list = ""; $sel = "";
	$result = dbquery("SELECT f.forum_id, f.forum_parent, f2.forum_name AS forum_cat_name FROM ".DB_FORUMS." f
	LEFT JOIN ".DB_FORUMS." f2 ON f2.forum_id=f.forum_id
	WHERE ".groupaccess('f.forum_access')." AND f2.forum_parent='$forum_id'");
	while($data = dbarray($result)){
	if ($data['forum_id'] == $_GET['forum_id']) { $sel = " selected"; } else { $sel = ""; }
		$move_list .= "<option value='".$data['forum_id']."'$sel>  -".$data['forum_cat_name']."</option>\n";
	}
	return $move_list;

}

//forum subcategories functions end

//download subcategories functions begin
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
function download_subdownloads($parent){

	$result = dbquery("SELECT download_cat_id, download_cat_name FROM ".DB_DOWNLOAD_CATS." WHERE download_cat_parent=$parent AND ".groupaccess('download_cat_access')."
		ORDER BY download_cat_name");
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
function download_admincats_subcats($id) {
	global $aidlink, $locale;$sublist = "";
$result = dbquery("SELECT download_cat_id, download_cat_name, download_cat_description, download_cat_access FROM ".DB_DOWNLOAD_CATS." WHERE download_cat_parent='".(int)$id."' ORDER BY download_cat_name");
	if (dbrows($result) != 0) {
		$i = 0;
		 
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
			$i++;
		}
	}
	
	return $sublist;
}
//download subcategories functions end
//article subcategories functions begin
function article_subcats($id, $type="") {
    global $locale;
 
	$result = dbquery("SELECT article_cat_id, article_cat_name, article_cat_description FROM ".DB_ARTICLE_CATS." WHERE ".groupaccess('article_cat_access')." AND article_cat_parent='".(int)$id."'  ORDER BY article_cat_name");
	$k = dbrows($result);
	if ($k > 0) {
		if ($type == "1") {
			$counter = 0; $columns = 2;
			echo "<span class='side'><strong>".$locale['404']."</strong></span><br />\n";
			
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		while ($data = dbarray($result)) {
			if ($counter != 0 && ($counter % $columns == 0)) { echo "</tr>\n<tr>\n"; }
			$num = dbcount("(article_cat)", DB_ARTICLES, "article_cat='".$data['article_cat_id']."' AND article_draft='0'");
			echo "<td valign='top' width='50%' class='tbl article_idx_cat_name'><!--article_idx_cat_name--><a href='".FUSION_SELF."?cat_id=".$data['article_cat_id']."'>".$data['article_cat_name']."</a> <span class='small2'>($num)</span>";
			if ($data['article_cat_description'] != "") { echo "<br />\n<span class='small'>".$data['article_cat_description']."</span>"; }
			echo "</td>\n";
			$counter++;
		}
		echo "</tr>\n</table>\n";
			echo "<div style='margin:5px'></div>";
		} else {	  
	        echo "<br />";
			echo "<span class='side'><strong>".$locale['404'].": </strong></span>";
			while ($data = dbarray($result)) {
				$k--;
				$num = dbcount("(article_cat)", DB_ARTICLES, "article_cat='".(int)$data['article_cat_id']."' AND article_draft='0'");
				echo "<!--article_idx_cat_name--><a href='".FUSION_SELF."?cat_id=".$data['article_cat_id']."'>".$data['article_cat_name']."</a> <span class='small2'>($num)</span>";
				if ($k > 0) echo  ", ";
			}
		}
	} 
}
function articles_admin_subcats($id) {
  global $aidlink, $locale, $data;
     
    $sublist = "";
			$result2 = dbquery("SELECT * FROM ".DB_ARTICLE_CATS." WHERE article_cat_parent='".(int)$id."' ORDER BY article_cat_name");
			while ($data2 = dbarray($result2)) {
			
			$sublist .= "<tr>\n";
			$sublist .= "<td class='tbl1'><strong>--".$data2['article_cat_name']."</strong><br />\n";
			$sublist .= "<span class='small'>".trimlink($data2['article_cat_description'], 45)."</span></td>\n";
			$sublist .= "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>".getgroupname($data2['article_cat_access'])."</td>\n";
			$sublist .= "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&action=edit&cat_id=".$data2['article_cat_id']."'>".$locale['443']."</a> -\n";
			$sublist .= "<a href='".FUSION_SELF.$aidlink."&action=delete&cat_id=".$data2['article_cat_id']."' onclick=\"return confirm('".$locale['450']."');\">".$locale['444']."</a></td>\n";
			$sublist .= "</tr>\n";
			
			}
			return $sublist;

}
function article_subarticle($id,$article_cat) {

	$result = dbquery("SELECT article_cat_id, article_cat_name FROM ".DB_ARTICLE_CATS." WHERE article_cat_parent='".(int)$id."' ORDER BY article_cat_name DESC");
	$subcat = "";$sel = "";
	if (dbrows($result) != 0) {
	$k = 0;
		
		while ($data = dbarray($result)) {
			if (isset($_GET['action']) && $_GET['action'] == "edit") { $sel = (isset($article_cat) && $article_cat == $data['article_cat_id'] ? " selected='selected'" : "");}
		$subcat .= "<option value='".$data['article_cat_id']."'$sel>".$data['article_cat_name']."</option>\n";
		$k++;	
		}
		
	} 
    return $subcat;	
}


?>