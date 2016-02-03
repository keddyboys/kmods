<?php

function refresh_subforums($parent){
	$result2 = dbquery("SELECT forum_id FROM ".DB_FORUMS." WHERE forum_parent='".$parent."' ORDER BY forum_order");
	$k = 1;
	$list = "";
	while ($data2 = dbarray($result2)) {
		$result3 = dbquery("UPDATE ".DB_FORUMS." SET forum_order='".$k."' WHERE forum_id='".$data2['forum_id']."'");
		$k++;
		$list .= refresh($data2['forum_id']);
	}
	return $list;
}

function forum_parent_subforums($data2, $result2, $k){
	
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

function forum_subforums($parent){

	$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_parent='$parent' ORDER BY forum_order asc");
	$forumR = "";
	
	if (dbrows($result)) {
		
		$k = 1;
		while ($data = dbarray($result)) {
			
			$forumR .= forum_parent_subforums($data, $result, $k);
			$k++;
			$forumR .= forum_subforums($data['forum_id']);
			
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

function jump_to_forum($forum_id){
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
function move_to_forum($forum_id){
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
?>