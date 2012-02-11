<?php
/* Random forum randomizer
 * By Kawa
 *
 * External requirements:
 *   None!
 *
 * Replace the 3 on line 20 with the forum ID to randomize
 * if your offtopic forum does not have ID 3.
 * The actual lists should be obvious.
 */

registerPlugin("Random forum randomizer");

function RandomForum_Apply($tag = "", $id = 0)
{
	global $forum;
	if($tag != "forumList")
		return;
	if($id == 1)
	{
		$forum['title'] = "General chat";
		$forum['description'] = "The place to talk about anything.";
	}
	if($id == 3)
	{
		$rndTitles = array(
			"Randomness dump",
			"Randomly Randomized Random Randomness",
			"LSD-induced hallucinations",
			"Spatula City",
			"?????",
			"Outer Haven",
			"Snakes on a Plane",
			"Snake's on a Plane",
			"The Closet",
			"Enrichment Center",
			"Anarchists Anonymous",
			"Staff Forum",
			"The Elite Four",
			"The Mushroom Kingdom",
			"Johto Region",
			"Kanto Region",
			"Hoenn Region",
			"Sinnoh Region",
			"Isshu Region",
			"Akihabara Electric Town",
			"Boothill Saloon",
			"The state of Denmark",
			"World of Goo",
			"Borealis",
			"Black Mesa",
			"Bowser's Castle",
			"The K&#x044F;e&#x043C;li&#x0438;",
			"The Homeship",
			"<img src=\"img/smilies/awsum.png\" alt=\"AWSUM\" />",
			"The Nine o' Clock <em>FORK</em>",
			"MISSINGNO.",
			"USS Enterprise",
			"Namek",
			"Jeopardy!",
			"The World!",
			"Moebius",
			"Silicon Valley",
			"Count Spatula",
			"Rainbow Rumpus Party Town",
		);
		$rndDescs = array(
			"<span style=\"-o-transform: rotate(180deg);-moz-transform:rotate(180deg);\">Hey look! I can type upside-down!</span>",
			"<span style=\"-o-transform: rotate(2deg);-moz-transform:rotate(2deg);\">Slightly Deranged</span>",
			"I need scissors! 61!",
			"Our shields cannot repel a spatula of that magnitude!",
			"WHAT!? 9000!!?",
			"The cake is a lie",
			"I&#x0327;&#x0341;&#x0328;t&#x0322; &#x0337i&#x031B;s&#x0361;&#x0489; &#x0358;c&#x0336;o&#x034F;mi&#x0360;n&#x0361;&#x0341;&#x0338;g&#x035E;.&#x031B;&#x0360;.&#x0358;&#x031B;&#x035E;.&#x0328;", //<zalgo>It is coming...</zalgo>
			"GO Speed Racer GO!",
			"Gotta catch 'em all!",
			"We need more cowbell",
			"Zeeky Boogy Doog",
			"I can has cheezburger?",
			"The forum that can't be colored any hue",
			"A miserable little pile of secrets",
			"Alas, poor Yorick&hellip;",
			"When I invite a woman to dinner I expect her to look at my beaver. That's the price she has to pay.",
			"&hellip;",
			"There's a movie on TV. Four boys are walking on railroad tracks. &hellip;I better go too.",
			"<span style=\"font-family: cursive;\">Elbereth</span>",
			"I like shorts. They're comfy and easy to wear.",
			"I like shorts. They're <em>delightfully</em> comfy and easy to wear.",
			"The balls are inert!",
			"Punch the keys for God's sake!",
			"You're the man now dog!",
			"And the answer is: 42",
			"WRYYYYYYYYYYYYYYYYYYYY!",
			"I'm waiting&hellip;",
			"It never hurts to help",
			"Let's get dangerous!",
			"&#x2669; Kill the wabbit, kill the wabbit!",
			"Hel<em>looooo</em> Nurse!",
			"That was a joke.",
			"Haha, fat chance!",
		);

		$forum['title'] = $rndTitles[array_rand($rndTitles)];
		$forum['description'] = $rndDescs[array_rand($rndDescs)];
	}
}

register("manglers", "RandomForum_Apply", 2);


?>