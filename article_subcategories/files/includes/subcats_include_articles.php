<?php
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
			$result2 = dbquery("SELECT article_cat_id, article_cat_name, article_cat_description, article_cat_access FROM ".DB_ARTICLE_CATS." WHERE article_cat_parent='".(int)$id."' ORDER BY article_cat_name");
			while ($data2 = dbarray($result2)) {
			
			$sublist .= "<tr>\n";
			$sublist .= "<td class='tbl1'><strong>-".$data2['article_cat_name']."</strong><br />\n";
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
		$subcat .= "<option value='".$data['article_cat_id']."'$sel>-".$data['article_cat_name']."</option>\n";
		$k++;	
		}
		
	} 
    return $subcat;	
}

function article_admin_editlist($id) {
	global $data;
	$editlist = ""; $sel = "";
	$checkparent = dbcount("(article_cat_id)", DB_ARTICLE_CATS, "article_cat_parent='".(int)$id."'");
	$result2 = dbquery("SELECT article_cat_id, article_cat_name FROM ".DB_ARTICLE_CATS." WHERE article_cat_parent='0' ORDER BY article_cat_name");
	if (dbrows($result2) != 0) {
	        $editlist .= "<option value='0'".$sel."><span class='small'></span></option>\n";
		while ($data2 = dbarray($result2)) {
			if (isset($_GET['action']) && $_GET['action'] == "edit") { $sel = ($data['article_cat_parent'] == $data2['article_cat_id'] ? " selected='selected'" : ""); }
			
			if ((isset($_GET['action']) && $_GET['action'] == "edit") &&  ($_GET['cat_id'] != $data2['article_cat_id']) && ($checkparent == 0)) {
                $editlist .= "<option value='".$data2['article_cat_id']."'$sel>".$data2['article_cat_name']."</option>\n";
			} elseif (!isset($_GET['action'])) {	
			    $editlist .= "<option value='".$data2['article_cat_id']."'$sel>".$data2['article_cat_name']."</option>\n";	
			}	
		}
	}
     return $editlist;
}
?>