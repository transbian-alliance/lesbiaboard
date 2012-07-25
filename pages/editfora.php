<?php

//Category/forum editor -- By Nikolaj
//Secured and improved by Dirbaio

$title = __("Edit forums");

if ($loguser['powerlevel'] < 3) Kill(__("You're not allowed to access the forum editor."));
MakeCrumbs(array(__("Admin") => actionLink("admin"), __("Edit forum list") => actionLink("editfora")), "");

/** 
	Okay. Much like the category editor, now the action is specified by $_POST["action"]. 

	Possible actions are:
	- updateforum: Updates the settings of a forum in the DB.
	- addforum: Adds a new forum to the DB.
	- deleteforum: Deletes a forum from the DB. Also, depending on $_GET["threads"]: (NOT YET)
		- "delete": DELETES all threads and posts in the DB.
		- "trash": TRASHES all the threads (move to trash and close)
		- "move": MOVES the threads to forum ID $_POST["threadsmove"]
		- "leave": LEAVES all the threads untouched in the DB (like the old forum editor. Not recommended. Will cause "invisible posts" that will still count towards user's postcounts)
	
	- forumtable: Returns the forum table for the left panel.
	- editforum: Returns the HTML code for the forum settings in right panel. 
		- editforumnew: Returns the forum edit box to create a new forum. This way the huge HTML won't be duplicated in the code.
		- editforum: Returns the forum edit box to edit a forum.

**/


//Make actions be requested by GET also. Makes AJAX stuff easier in some cases. And manual debugging too :)
if(!isset($_POST["action"]))
	$_POST["action"] = $_GET["action"];

$noFooter = true;

switch($_POST['action'])
{
	case 'updateforum':
	
		//Check for the key
		if (isset($_POST['action']) && $loguser['token'] != $_POST['key'])
			Kill(__("No."));

		//Get new forum data
		$id = (int)$_POST['id'];
		$title = $_POST['title'];
		$description = $_POST['description'];
		$category = (int)$_POST['category'];
		$forder = (int)$_POST['forder'];
		$minpower = (int)$_POST['minpower'];
		$minpowerthread = (int)$_POST['minpowerthread'];
		$minpowerreply = (int)$_POST['minpowerreply']; 
		
		//Send it to the DB
		Query("UPDATE {forums} SET title = {0}, description = {1}, catid = {2}, forder = {3}, minpower = {4}, minpowerthread = {5}, minpowerreply = {6} WHERE id = {7}", $title, $description, $category, $forder, $minpower, $minpowerthread, $minpowerreply, $id);
		dieAjax("Ok");

		break;
	case 'updatecategory':
	
		//Check for the key
		if (isset($_POST['action']) && $loguser['token'] != $_POST['key'])
			Kill(__("No."));

		//Get new cat data
		$id = (int)$_POST['id'];
		$name = $_POST['name'];
		$corder = (int)$_POST['corder'];
		
		//Send it to the DB
		Query("UPDATE {categories} SET name = {0}, corder = {1} WHERE id = {2}", $name, $corder, $id);
		dieAjax("Ok");

		break;
		
	case 'addforum':
		//Check for the key
		if (isset($_POST['action']) && $loguser['token'] != $_POST['key'])
			Kill(__("No."));
	
		//Get new forum data
		$title = $_POST['title'];
		$description = $_POST['description'];
		$category = (int)$_POST['category'];
		$forder = (int)$_POST['forder'];
		$minpower = (int)$_POST['minpower'];
		$minpowerthread = (int)$_POST['minpowerthread'];
		$minpowerreply = (int)$_POST['minpowerreply'];

		//Figure out the new forum ID.
		//I think it'd be better to use InsertId, but...
		$newID = FetchResult("SELECT id+1 FROM {forums} WHERE (SELECT COUNT(*) FROM {forums} f2 WHERE f2.id={forums}.id+1)=0 ORDER BY id ASC LIMIT 1");
		if($newID < 1) $newID = 1;

		//Add the actual forum
		Query("INSERT INTO {forums} (`id`, `title`, `description`, `catid`, `forder`, `minpower`, `minpowerthread`, `minpowerreply`) VALUES ({0}, {1}, {2}, {3}, {4}, {5}, {6}, {7})", $newID, $title, $description, $category, $forder, $minpower, $minpowerthread, $minpowerreply);
		
		dieAjax("Ok");

	case 'addcategory':
	
		//Check for the key
		if (isset($_POST['action']) && $loguser['token'] != $_POST['key'])
			Kill(__("No."));

		//Get new cat data
		$id = (int)$_POST['id'];
		$name = $_POST['name'];
		$corder = (int)$_POST['corder'];
		
		//Send it to the DB

		//Add the actual forum
		Query("INSERT INTO {categories} (`name`, `corder`) VALUES ({0}, {1})", $name, $corder);

		dieAjax("Ok");

		break;
	case 'deleteforum':
		//TODO: Move and delete threads mode.

		//Check for the key
		if (isset($_POST['action']) && $loguser['token'] != $_POST['key'])
			Kill(__("No."));
		
		//Get Forum ID
		$id = (int)$_POST['id'];
		
		//Check that forum exists
		$rForum = Query("SELECT * FROM {forums} WHERE id={0}", $id);
		if (!NumRows($rForum))
			dieAjax("No such forum.");
		
		//Check that forum has threads.
		$forum = Fetch($rForum);
		if($forum['numthreads'] > 0)
			dieAjax(__("Forum has threads. Move those first."));
			
		//Delete
		Query("DELETE FROM `{forums}` WHERE `id` = {0}", $id);
		dieAjax("Ok");
	case 'deletecategory':
		//TODO: Do something with the forums left in it?
		
		//Check for the key
		if (isset($_POST['action']) && $loguser['token'] != $_POST['key'])
			Kill(__("No."));
		
		//Get Cat ID
		$id = (int)$_POST['id'];
		
		//Check that forum exists
		$rCat = Query("SELECT * FROM {categories} WHERE id={0}", $id);
		if (!NumRows($rCat))
			dieAjax(__("No such category."));
		
		//Delete
		Query("DELETE FROM `{categories}` WHERE `id` = {0}", $id);
		dieAjax("Ok");
		
	case 'forumtable':
		writeForumTableContents();
		dieAjax("");
		break;
		
	case 'editforumnew':
	case 'editforum':
	
		//Get forum ID
		$fid = (int)$_GET["fid"];
		if($_POST['action'] == 'editforumnew')
			$fid = -1;
			
		WriteForumEditContents($fid);
		dieAjax("");
		break;

	case 'editcategorynew':
	case 'editcategory':
	
		//Get cat ID
		$cid = (int)$_GET["cid"];
		if($_POST['action'] == 'editcategorynew')
			$cid = -1;
			
		WriteCategoryEditContents($cid);
		dieAjax("");
		break;
		
	case '': //No action, do main code
		break;
	
	default: //Unrecognized action
		dieAjax(format(__("Unknown action: {0}"), $_POST["action"]));
}



//Main code.

print '<script src="js/editfora.js" type="text/javascript"></script>';

Write('
<div id="editcontent" style="float: right; width: 45%;">
	&nbsp;
</div>
<div id="flist">
');

WriteForumTableContents();

Write('
</div>');




//Helper functions

function cell()
{
	global $cell;
	$cell = ($cell == 1 ? 0 : 1);
	return $cell;
}

// $fid == -1 means that a new forum should be made :)
function WriteForumEditContents($fid)
{
	global $loguser;

	//Get all categories.
	$rCats = Query("SELECT * FROM {categories}");

	$cats = array();
	while ($cat = Fetch($rCats))
		$cats[$cat['id']] = $cat;
	
	if(count($cats) == 0)
		$cats[0] = __("No categories");

	if($fid != -1)
	{
		$rForum = Query("SELECT * FROM {forums} WHERE id={0}", $fid);
		if (!NumRows($rForum))
		{
			Kill(__("Forum not found."));
		}
		$forum = Fetch($rForum);

		$title = htmlspecialchars($forum['title']);
		$description = htmlspecialchars($forum['description']);
		$catselect = MakeCatSelect('cat', $cats, $forum['catid']);
		$minpower = PowerSelect('minpower', $forum['minpower']);
		$minpowerthread = PowerSelect("minpowerthread", $forum['minpowerthread']);
		$minpowerreply = PowerSelect('minpowerreply', $forum['minpowerreply']);
		$forder = $forum['forder'];
		$func = "changeForumInfo";
		$button = __("Update");
		$boxtitle = __("Edit Forum");
		$delbutton = "
			<button onclick='showDeleteForum(); return false;'>
				".__("Delete")."
			</button>";
	}
	else
	{
		$title = __("New Forum");
		$description = __("Description goes here. <strong>HTML allowed.</strong>");
		$catselect = MakeCatSelect('cat', $cats, 1);
		$minpower = PowerSelect('minpower', 0);
		$minpowerthread = PowerSelect("minpowerthread", 0);
		$minpowerreply = PowerSelect('minpowerreply', 0);
		$forder = 0;
		$func = "addForum";
		$button = __("Add");
		$boxtitle = __("New Forum");
		$delbutton = "";
	}
	
	Write('
	<form method="post" id="forumform" action="'.actionLink("editfora").'">
	<input type="hidden" name="key" value="{8}">
	<input type="hidden" name="id" value="{6}">
	<table class="outline margin">
		<tr class="header1">
			<th colspan="2">
				{11}
			</th>
		</tr>
		<tr class="cell1">
			<td style="width: 25%;">
				'.__("Title").'
			</td>
			<td>
				<input type="text" style="width: 98%;" name="title" value="{0}" />
			</td>
		</tr>
		<tr class="cell1">

			<td>
				'.__("Description").'
			</td>
			<td>
				<input type="text" style="width: 98%;" name="description" value="{1}" />
			</td>
		</tr>
		<tr class="cell0">
			<td>
				'.__("Category").'
			</td>
			<td>
				{2}
			</td>
		</tr>
		<tr class="cell1">
			<td>
				'.__("Listing order").'
			</td>
			<td>
				<input type="text" size="2" name="forder" value="{7}" />
				<img src="img/icons/icon5.png" title="'.__("Everything is sorted by listing order first, then by ID. If everything has its listing order set to 0, they will therefore be sorted by ID only.").'" alt="[?]" />
			</td>
		</tr>
		<tr class="cell0">
			<td>
				'.__("Powerlevel required").'
			</td>
			<td>

				{3}
				'.__("to view").'
				<br />
				{4}
				'.__("to post threads").'
				<br />
				{5}
				'.__("to reply").'
			</td>
		</tr>
		<tr class="cell2">
			<td>
				&nbsp;
			</td>
			<td>
				<button onclick="{9}(); return false;">
					{10}
				</button>
				{12}
			</td>
		</tr>
	</table></form>
	
	<form method="post" id="deleteform" action="'.actionLink("editfora").'">
	<input type="hidden" name="key" value="{8}">
	<input type="hidden" name="id" value="{6}">
	<div id="deleteforum" style="display:none">
		<table>
			<tr class="header1">

				<th>
					'.__("Delete forum").'
				</th>
			</tr>
			<tr class="cell0">
				<td>
					'.__("Instead of deleting a forum, you might want to consider archiving it: Change its name or description to say so, and raise the minimum powerlevel to reply and create threads so it's effectively closed.").'<br><br>
					'.__("If you still want to delete it, click below:").'<br>
					<button onclick="deleteForum(\'delete\'); return false;">
						'.__("Delete forum").'
					</button>
				</td>
			</tr>
		</table>
	</div>
	</form>	
	
	', $title, $description, $catselect, $minpower, $minpowerthread, $minpowerreply, $fid, $forder, $loguser['token'], $func, $button, $boxtitle, $delbutton);
}
// $fid == -1 means that a new forum should be made :)
function WriteCategoryEditContents($cid)
{
	global $loguser;

	//Get all categories.
	$rCats = Query("SELECT * FROM {categories}");

	$cats = array();
	while ($cat = Fetch($rCats))
		$cats[$cat['id']] = $cat;
	
	if(count($cats) == 0)
		$cats[0] = "No categories";

	if($cid != -1)
	{
		$rCategory = Query("SELECT * FROM {categories} WHERE id={0}", $cid);
		if (!NumRows($rCategory))
		{
			Kill("Category not found.");
		}
		$cat = Fetch($rCategory);

		$name = htmlspecialchars($cat['name']);
		$corder = $cat['corder'];

		$func = "changeCategoryInfo";
		$button = __("Update");
		$boxtitle = __("Edit Category");
		$delbutton = "
			<button onclick='showDeleteForum(); return false;'>
				".__("Delete")."
			</button>";
	}
	else
	{
		$title = __("New Category");
		$corder = 0;
		$func = "addCategory";
		$button = __("Add");
		$boxtitle = __("New Category");
		$delbutton = "";
	}
	
	print '
	<form method="post" id="forumform" action="'.actionLink("editfora").'">
	<input type="hidden" name="key" value="'.$loguser['token'].'">
	<input type="hidden" name="id" value="'.$cid.'">
	<table class="outline margin">
		<tr class="header1">
			<th colspan="2">
				'.$boxtitle.'
			</th>
		</tr>
		<tr class="cell1">
			<td style="width: 25%;">
				'.__("Name").'
			</td>
			<td>
				<input type="text" style="width: 98%;" name="name" value="'.$name.'" />
			</td>
		</tr>
		<tr class="cell0">
			<td>
				'.__("Listing order").'
			</td>
			<td>
				<input type="text" size="2" name="corder" value="'.$corder.'" />
				<img src="img/icons/icon5.png" title="'.__("Everything is sorted by listing order first, then by ID. If everything has its listing order set to 0, they will therefore be sorted by ID only.").'" alt="[?]" />
			</td>
		</tr>
		<tr class="cell2">
			<td>
				&nbsp;
			</td>
			<td>
				<button onclick="'.$func.'(); return false;">
					'.$button.'
				</button>
				'.$delbutton.'
			</td>
		</tr>
	</table></form>
	
	<form method="post" id="deleteform" action="'.actionLink("editfora").'">
	<input type="hidden" name="key" value="'.$loguser['token'].'">
	<input type="hidden" name="id" value="'.$cid.'">
	<div id="deleteforum" style="display:none">
		<table>
			<tr class="header1">

				<th>
					'.__("Delete category").'
				</th>
			</tr>
			<tr class="cell0">
				<td>
					'.__("Be careful when deleting categories. Make sure there are no forums in the category before deleting it.").'
					<br><br>
					'.__("If you still want to delete it, click below:").'
					<br>
					<button onclick="deleteCategory(\'delete\'); return false;">
						'.__("Delete category").'
					</button>
				</td>
			</tr>
		</table>
	</div>
	</form>';
}


function WriteForumTableContents()
{
	$cats = array();
	$rCats = Query("SELECT * FROM {categories} ORDER BY corder, id");
	$forums = array();
	if (NumRows($rCats))
	{
		while ($cat = Fetch($rCats))
		{
			$cats[$cat['id']] = $cat;
		}
		$rForums = Query("SELECT * FROM {forums} ORDER BY forder, id");
		$forums = array();
		if (NumRows($rForums)) {
			while ($forum = Fetch($rForums))
			{
				$forums[$forum['id']] = $forum;
			}
		}
	}
	$hint = $cats ? __("Hint: Click a forum to select it.") : '';
	$newforum = $cats ? '<button onclick="newForum();">'.__("Add Forum").'</button>' : '';
	
	$buttons = '
	<tr class="cell2">
		<td>
			<span style="float: right;">' . $newforum .
				'<button onclick="newCategory();">'.__("Add Category").'</button>
			</span>' . $hint . '
		</td>
	</tr>';

	print '
	<table class="outline margin" style="width: 45%;">
	<tr class="header1">
		<th>
			'.__("Edit forum list").'
		</th>
	</tr>';
	print $buttons;
	foreach ($forums as $forum)
	{
		$cats[$forum['catid']]['forums'][$forum['id']] = $forum;
	}
	
	foreach ($cats as $cat)
	{
		print '
	<tbody id="cat'.$cat['id'].'" class="c">
		<tr class="cell'.cell().'">
			<td class="c" onmousedown="pickCategory('.$cat['id'].');">
				<strong>'.$cat['name'].'</strong>
			</td>
		</tr>';
		
		if(isset($cat['forums'])) //<Kawa> empty categories look BAD.
		{
			foreach ($cat['forums'] as $cf)
			{
				$sel = $_GET['s'] == $cf['id'] ? ' outline: 1px solid #888;"' : '';
				print '
		<tr class="cell'.cell().'" style="cursor: hand;">
			<td style="padding-left: 24px;'.$sel.'" class="f" onmousedown="pickForum('.$cf['id'].');" id="forum'.$cf['id'].'">
				'.$cf['title'].'<br />
				<small style="opacity: 0.75;">'.$cf['description'].'</small>
			</td>
		</tr>';
			}
		}
		else
		{
				print '
		<tr class="cell'.cell().'" style="cursor: hand;">
			<td style="padding-left: 24px;" class="f">
				'.__("No forums in this category.").'
			</td>
		</tr>';
		}
		print "</tbody>";
	}

	if ($forums) {
	print $buttons;
	}
	print '</table>';
}

function MakeCatSelect($i, $o, $v)
{
	$r = '
			<select name="category">';
	foreach ($o as $opt)
	{
		$r .= '
				<option value="'.$opt['id'].'"'.($v == $opt['id'] ? ' selected="selected"' : '').'>
					'.$opt['name'].'
				</option>';
	}
	$r .= '
			</select>';
	return $r;
}
function PowerSelect($id, $s)
{
	$r = Format('
				<select name="{0}">
	', $id);
	$powers = array(-1=>__("Banned"), 0=>__("Regular"), 1=>__("Local mod"), 2=>__("Full mod"), 3=>__("Admin"));
	foreach ($powers as $k => $v)
	{
		$r .= Format('
					<option value="{0}"{2}>{1}</option>
		', $k, $v, ($k == $s ? ' selected="selected"' : ''));
	}
	$r .= '
				</select>';
	return $r;
}

