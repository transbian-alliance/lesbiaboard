<?php
/* Profile Field Extensions
 * By Kawa
 *
 * External requirements:
 *   None!
 *
 * To use, check "Edit Profile".
 */

//THIS is an important variable. If a user convinces you that you need to have more fields, this is what you change:
$numberOfFields = 10;
//The rest of the file is irrelevant.




registerPlugin("Profile Field Extensions");
for($i = 0; $i < $numberOfFields; $i++)
{
	registerSetting("profileExt".$i."t", "Custom profile field #".($i+1)." title");
	registerSetting("profileExt".$i."v", "Custom profile field #".($i+1)." value");
}

function ProfileFields_Write()
{
	global $user, $numberOfFields;
	
	for($i = 0; $i < $numberOfFields; $i++)
	{
		if(getSetting("profileExt".$i."t", true) != "")
		{
			write(
"
				<tr>
					<td class=\"cell0\">{0}</td>
					<td class=\"cell1\">{1}</td>
				</tr>
", strip_tags(getSetting("profileExt".$i."t", true)), CleanUpPost(getSetting("profileExt".$i."v", true)));
		}
	}
}

register("profileTable", "ProfileFields_Write");

?>