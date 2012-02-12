<?php

//Category/forum editor -- By Nikolaj
//Secured and improved by Dirbaio

$title = "Edit forums";

if ($loguser['powerlevel'] < 3) Kill("You're not allowed to access the forum editor.");

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

$key = hash('sha256', "{$loguserid},{$loguser['pss']},{$salt}");

$noFooter = true;

switch($_POST['action'])
{
	case 'updateforum':
	
		//Check for the key
		if (isset($_POST['action']) && $key != $_POST['key'])
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
		$qForum = "UPDATE forums SET title = '".justEscape($title)."', description = '".justEscape($description)."', catid = ".$category.", forder = ".$forder.", minpower = ".$minpower.", minpowerthread = ".$minpowerthread.", minpowerreply = ".$minpowerreply." WHERE id = ".$id;
		Query($qForum);
		dieAjax("Ok");

		break;
		
	case 'addforum':
		//Check for the key
		if (isset($_POST['action']) && $key != $_POST['key'])
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
		//I think it'd be better to use mysql_insert_id, but...
		$newID = FetchResult("SELECT id+1 FROM forums WHERE (SELECT COUNT(*) FROM forums f2 WHERE f2.id=forums.id+1)=0 ORDER BY id ASC LIMIT 1");
		if($newID < 1) $newID = 1;

		//Add the actual forum
		$qForum = "INSERT INTO forums (`id`, `title`, `description`, `catid`, `forder`, `minpower`, `minpowerthread`, `minpowerreply`) VALUES (".$newID.", '".justEscape($title)."', '".justEscape($description)."', ".$category.", ".$forder.", ".$minpower.", ".$minpowerthread.", ".$minpowerreply.")";
		Query($qForum);
		
		dieAjax("Ok");
		
	case 'deleteforum':
		//TODO: Move and delete threads mode.

		//Check for the key
		if (isset($_POST['action']) && $key != $_POST['key'])
			Kill(__("No."));
		
		//Get Forum ID
		$id = (int)$_POST['id'];
		
		//Check that forum exists
		$qForum = "SELECT * FROM forums WHERE id=".$id;
		$rForum = Query($qForum);
		if (!NumRows($rForum))
			dieAjax("No such forum.");
		
		//Check that forum has threads.
		$forum = Fetch($rForum);
		if($forum['numthreads'] > 0)
			dieAjax("Forum has threads. Move those first.");
			
		//Delete
		Query("DELETE FROM `forums` WHERE `id` = ".$id);
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
		
	case '': //No action, do main code
		break;
	
	default: //Unrecognized action
		dieAjax("Unknown action: ".$_POST["action"]);
}



//Main code.

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

function cell() {
	global $cell;
	$cell = ($cell == 1 ? 0 : 1);
	return $cell;
}

// $fid == -1 means that a new forum should be made :)
function WriteForumEditContents($fid)
{
	global $key;

	//Get all categories.
	$qCats = "SELECT * FROM categories";
	$rCats = Query($qCats);

	$cats = array();
	while ($cat = Fetch($rCats))
		$cats[$cat['id']] = $cat;
	
	if(count($cats) == 0)
		$cats[0] = "No categories";

	if($fid != -1)
	{
		$qForum = "SELECT * FROM forums WHERE id=".$fid;
		$rForum = Query($qForum);
		if (!NumRows($rForum)) {
			Kill("Forum not found.");
		}
		$forum = Fetch($rForum);

		$title = $forum['title'];
		$description = $forum['description'];
		$catselect = MakeCatSelect('cat', $cats, $forum['catid']);
		$minpower = PowerSelect('minpower', $forum['minpower']);
		$minpowerthread = PowerSelect("minpowerthread", $forum['minpowerthread']);
		$minpowerreply = PowerSelect('minpowerreply', $forum['minpowerreply']);
		$forder = $forum['forder'];
		$func = "changeForumInfo";
		$button = "Update";
		$boxtitle = "Edit Forum";
		$delbutton = "
			<button onclick='showDeleteForum(); return false;'>
				Delete
			</button>";
	}
	else
	{
		$title = "New Forum";
		$description = "Description goes here. <b>HTML allowed</b>";
		$catselect = MakeCatSelect('cat', $cats, 1);
		$minpower = PowerSelect('minpower', 0);
		$minpowerthread = PowerSelect("minpowerthread", 0);
		$minpowerreply = PowerSelect('minpowerreply', 0);
		$forder = 0;
		$func = "addForum";
		$button = "Add";
		$boxtitle = "New Forum";
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
				Title
			</td>
			<td>
				<input type="text" style="width: 98%;" name="title" value="{0}" />
			</td>
		</tr>
		<tr class="cell1">
			<td>
				Description
			</td>
			<td>
				<input type="text" style="width: 98%;" name="description" value="{1}" />
			</td>
		</tr>
		<tr class="cell0">
			<td>
				Category
			</td>
			<td>
				{2}
			</td>
		</tr>
		<tr class="cell1">
			<td>
				Listing order
			</td>
			<td>
				<input type="text" size="2" name="forder" value="{7}" />
				<img src="img/icons/icon5.png" title="Forums are sorted by listing order first, then by ID. If all forums in a category have their listing order set to 0, they will therefore be sorted by ID only." alt="[?]" />
			</td>
		</tr>
		<tr class="cell0">
			<td>
				Powerlevel required
			</td>
			<td>
				{3}
				to view
				<br />
				{4}
				to post threads
				<br />
				{5}
				to reply
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
					Delete forum
				</th>
			</tr>
			<tr class="cell0">
				<td>
					Instead of deleting a forum, you might want to consider "archiving" it: Change its name or description to say so, and raise the minimum powerlevel to reply and create threads so it\'s effectively closed.<br><br>
					If you still want to delete it, click below:<br>
					<button onclick="deleteForum(\'delete\'); return false;">
						Delete forum.
					</button>
				</td>
			</tr>
		</table>
	</div>
	</form>	
	
	', $title, $description, $catselect, $minpower, $minpowerthread, $minpowerreply, $fid, $forder, $key, $func, $button, $boxtitle, $delbutton);
	
	/*
					<br>
					<button onclick="deleteForum(\'trash\'); return false;">
						Trash all threads
					</button><br>
					<button onclick="deleteForum(\'move\'); return false;">
						Move all threads to:
					</button><br>
					<button onclick="deleteForum(\'leave\'); return false;">
						Leave threads in the DB
					</button> (NOT recommended)<br><br>
					<button onclick="hideDeleteForum(); return false;">
						
					</button> */
}

function WriteForumTableContents()
{
	$cats = array();
	$qCats = "SELECT * FROM categories ORDER BY corder, id";
	$rCats = Query($qCats);
	if (NumRows($rCats)) {
		while ($cat = Fetch($rCats)) {
			$cats[$cat['id']] = $cat;
		}
		$qForums = "SELECT * FROM forums ORDER BY forder, id";
		$rForums = Query($qForums);
		$forums = array();
		if (NumRows($rForums)) {
			while ($forum = Fetch($rForums)) {
				$forums[$forum['id']] = $forum;
			}
		}
	}

	$ftbl = Format('
	<table class="outline margin" style="width: 45%;">
	<tr class="header1">
		<th>
			Edit forums
		</th>
	</tr>');
	foreach ($forums as $forum) {
		$cats[$forum['catid']]['forums'][$forum['id']] = $forum;
	}
	foreach ($cats as $cat) {
		$ftbl .= Format('
	<tbody id="cat{1}" class="c">
		<tr class="cell{2}">
			<td class="c">
				<strong>{0}</strong>
			</td>
		</tr>', $cat['name'], $cat['id'], cell());
		if(isset($cat['forums'])) //<Kawa> empty categories look BAD.
		{
			foreach ($cat['forums'] as $cf) {
				$ftbl .= Format('
		<tr class="cell{3}" style="cursor: hand;">
			<td style="padding-left: 24px;{4}" class="f" onclick="pickForum({1});" id="forum{1}">
				{0}<br />
				<small style="opacity: 0.75;">{2}</small>
			</td>
		</tr>', $cf['title'], $cf['id'], $cf['description'], cell(), ($_GET['s'] == $cf['id'] ? ' outline: 1px solid #888;"' : ''));
			}
		}
		$ftbl .= Format("
	</tbody>");
	}
	
	$ftbl .= Format('
	<tr class="cell2">
		<td>
			<span style="float: right;">
				<button onclick="newForum();">Add Forum</button>
			</span>Hint: Click a forum to select it.
		</td>
	</tr></table>');
			
	print $ftbl;
}

function MakeCatSelect($i, $o, $v) {
	$r = '
			<select name="category">';
	foreach ($o as $opt) {
		$r .= '
				<option value="'.$opt['id'].'"'.($v == $opt['id'] ? ' selected="selected"' : '').'>
					'.$opt['name'].'
				</option>';
	}
	$r .= '
			</select>';
	return $r;
}
function PowerSelect($id, $s) {
	$r = Format('
				<select name="{0}">
	', $id);
	$powers = array(-1=>"Banned", 0=>"Regular", 1=>"Local mod", 2=>"Full mod", 3=>"Admin");
	foreach ($powers as $k => $v) {
		$r .= Format('
					<option value="{0}"{2}>{1}</option>
		', $k, $v, ($k == $s ? ' selected="selected"' : ''));
	}
	$r .= '
				</select>';
	return $r;
}

//Sort array by values in sub-arrays
//This will not work if the values in the sub-arrays are the same, but since this is made for ordering forums anyway, who cares?
function sort_by_order($array, $key, $order_column = "forder") {
	$r = array();
	foreach ($array as $k => $v) {
		$r[$v[$order_column]] = $v;
	}
	return $r;
}
