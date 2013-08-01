"use strict";

/* This plugin adds titles to the board header sort of like those in
 * Minecraft.
 *
 * ~Nina
 */

window.addEventListener("load", function() {
	var titles = [
		"Open source!",
		"...and freely distributable!",
		"Available on GitHub!",
		"Supports AJAX!",
		"AcmlmBoard XD!",
		"May contain JavaScript!",
		"Fork it!",
		"Internationalized!",
		"What?",
		"Using PHP!",
		":() { :|: & };:",
		"Not made by Acmlm!",
		"Engage!",
		"Call to undefined function!",
		"Colorful!",
		"Doesn't use Tidy!",
		"Probably has bugs!"
	];

	var title = titles[Math.round(Math.random() * (titles.length - 1))];

	var boardHeader = document.getElementById("theme_banner");
	var mcTitle = document.createElement("span");
	mcTitle.className = "mcTitle";
	mcTitle.appendChild(document.createTextNode(title));

	boardHeader.parentNode.parentNode.insertBefore(mcTitle, boardHeader.parentNode.nextSibling);

}, false);
