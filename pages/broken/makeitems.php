<?php

include("lib/common.php");

print "<pre>";

include("rpgfunctions.php");

Query("truncate table rpgitems");

$items = array
(
	//WEAPONS
	array( "Nothing", "Nothing. At All.", 0, 0, "", 0 ),
	//array( "Medigun", "Wait, what happened to <em>hurting</em> your enemies?", 0, 64500, "HP * 1.00, TP * 1.00, Int + 30, Spd * 0.80" ),
	//array( "Aleph-null", "Your fragile human minds cannot comprehend the powers that transcend infinity!", 0, 8388607, "HP * 32.67, TP * 32.67, Str * 60.84, Def * 61.92, Int * 42.84, SDf * 60.92, Dex * 120.92, Lck * 40.96, Spd * 35.00" ),
	//array( "Hyper Beam", "A weapon wrought from stolen evil.", 0, 8388607, "Str + 32767, Lck + 275, Spd + 100" ),
	array( "Herring", "Cut down the mightiest tree in the woods with it.", 0, 20, "Str + 1, Int - 3", 8 ),
	array( "Tuna Fish", "Dance around your opponent and slap 'em.", 0, 40, "Str + 5, Int - 5, Spd + 20", 8 ),
	array( "Lead pipe", "BARF!", 0, 40, "Str + 10" ),
	array( "Wooden sword", "It's dangerous to go alone! Take this!", 0, 50, "Str + 20" ),
	array( "Meat cleaver", "Red paint coating optional.", 0, 500, "Str + 30, Int - 20", 7 ),
	array( "Broken sword", "It may be small and broken, but the handle still works.", 0, 50, "Str + 5, Spd * 1.05", 9 ),
	array( "Wooden plank", "Pie Jesu domine... Dona eis requiem *SMACK*", 0, 500, "Str + 40" ),
	array( "Cheap piece of crap", "Half a katana.", 0, 700, "Str + 20, Spd * 1.10", 9 ),
	array( "Shuriken", "Meant for throwing.", 0, 900, "Str + 30", 10 ),
	array( "Cutlass", "The buccaneer's trusted sword", 0, 3000, "Str + 100" ),
	array( "Odd boomerang", "*cough*itsaplate*cough*", 0, 100, "Str + 35, Int - 5, Spd + 5", 14 ),
	array( "Cracked Limp Bizkit CD", "Sliiicing through your skiiin~", 0, 50, "Str + 35, Int - 15", 14 ),
	array( "Cardboard Tube", "Used to contain a Justin Bieber poster.", 0, 1000, "Str + 42, Int - 10", 11 ),
	array( "Katana", "But <a href=\"http://tvtropes.org/pmwiki/pmwiki.php/Main/EveryJapaneseSwordIsAKatana\">is it</a> really?", 0, 1200, "Str + 50", 6 ),
	array( "Kendo stick", "The True Blunder approved.", 0, 1400, "Str + 40, Spd * 1.50", 6 ),
	array( "Crowbar", "Does that say Black Mesa on the side?", 0, 8500, "Str + 80", 12 ),
	array( "Ban hammer", "No, it doesn't let you ban others.", 0, 20000, "Str + 200, Int + 50", 7 ),
	array( "Wrinklefucker", "It's a pogo hammer with steam iron heads :D", 0, 30000, "Str + 220, Int + 25, Spd * 0.8", 7 ),
	array( "Master Sword", "The legendary sword of Evil's Bane!", 0, 40000, "Str + 300" ),
	array( "Fear No Anvil", "Surely enough, it can dispatch a Lich with little trouble.", 0, 50000, "Str + 300, Int - 50, Spd * 0.5", 7 ),
	array( "Adamantine short sword", "This is a &curren;masterful adamantine short sword&curren; created by an unknown artisan.", 0, 90000, "HP + 126, TP - 10, Str + 267, Def + 53, Int - 5, SDf + 5, Dex - 10, Lck + 50, Spd - 15" ),
	//Lazor beam
	//Frying Pan

/*
	array( "Plastic knife", "Stabbity Stabbity, but much less painful.", 0, 50, "Str + 5" ),
	array( "Rubber spatula", "Flap Flap Smack", 0, 100, "Str + 5, Int - 2, Dex + 2" ),
	array( "Kitchen knife", "Stabbity Stabbity.", 0, 200, "Str + 11, Def + 1, Dex + 1, Spd + 1" ),
	array( "Butcher knife", "Now with red paint coating!", 0, 500, "HP + 5, Str + 21, Def + 2, Dex + 2, Spd + 1" ),
	array( "Short sword", "Gets the Urist McDwarf Seal of Approval!", 0, 1000, "HP + 13, TP - 1, Str + 32, Def + 5, Int - 1, Spd - 1" ),
	array( "Broad sword", "Long flat pain.", 0, 4000, "HP + 21, TP - 2, Str + 54, Def + 8, Int - 2, Spd - 1" ),
	array( "Iron sword", "Guaranteed to cut almost anything.", 0, 12000, "HP + 48, TP - 3, Str + 88, Def + 11, Int - 3, Dex - 1, Spd - 3" ),
	array( "Bronze sword", "THIS! IS! BRONZE!", 0, 21500, "HP + 65, TP - 5, Str + 136, Def + 16, Int - 5, Dex - 2, Spd - 5" ),
	array( "Silver sword", "Shiny.", 0, 46000, "HP + 93, TP - 8, Str + 182, Def + 34, Int - 8, Dex - 5, Lck + 10, Spd - 8" ),
	array( "Gold sword", "Shinier.", 0, 90000, "HP + 126, TP - 10, Str + 267, Def + 53, Int - 5, SDf + 5, Dex - 10, Lck + 50, Spd - 15" ),
	array( "Crystal sword", "Refractive goodness", 0, 150000, "HP + 139, TP + 5, Str + 409, Def + 86, Int + 5, SDf + 12, Dex - 12, Lck + 65, Spd - 23" ),
	array( "Dragon sword", "You won't have any trouble 'dragon' this one around.", 0, 380000, "HP + 259, Str + 726, Def + 120, SDf + 33, Dex - 38, Lck + 80, Spd - 75" ),
	array( "Beamsabre", "Lightweight and powerful, but very expensive", 0, 1065000, "Str * 1.50, Dex - 5, Spd + 20" ),
	array( "Wooden axe", "Not going to cut much with this", 0, 600, "HP * 1.02, TP * 0.98, Str + 35, Def + 2, Int - 4, SDf - 3, Dex - 5, Spd * 0.95" ),
	array( "Short axe", "Short, stout, and sharp.", 0, 2500, "HP * 1.05, TP * 0.95, Str + 66, Def + 5, Int - 8, SDf - 8, Dex - 11, Spd * 0.92" ),
	array( "Large axe", "Bigger. Sharper. Meaner.", 0, 8000, "HP * 1.10, TP * 0.88, Str + 102, Def + 8, Int - 21, SDf - 18, Dex - 16, Spd * 0.80" ),
	array( "Bronze axe", "I'd like to place an order for 300, please.", 0, 32000, "HP * 1.16, TP * 0.82, Str + 197, Def + 15, Int - 39, SDf - 36, Dex - 45, Spd * 0.72" ),
	array( "Silver axe", "Shiny slicer", 0, 66000, "HP * 1.19, TP * 0.79, Str + 302, Def + 25, Int - 68, SDf - 52, Dex - 71, Lck + 5, Spd * 0.69" ),
	array( "Gold axe", "Shinier Slicier", 0, 120000, "HP * 1.36, TP * 0.75, Str + 487, Def + 46, Int - 108, SDf - 101, Dex - 126, Lck + 12, Spd * 0.55" ),
	array( "Dragon axe", "...Is this thing even legal?", 0, 500000, "HP * 1.52, TP * 0.67, Str + 1339, Def + 70, Int - 161, SDf - 150, Dex - 192, Lck + 35, Spd * 0.47" ),
	array( "Small bow", "whif whif whif", 0, 400, "Str + 19, Dex + 5, Spd + 3" ),
	array( "Wooden bow", "Useless.", 0, 1500, "Str + 33, Int + 1, Dex + 9, Spd + 5" ),
	array( "Elven bow", "Now with 3x firing!", 0, 6000, "Str + 52, Int + 2, Dex + 15, Spd + 8" ),
	array( "Bronze crossbow", "Heavy.", 0, 20000, "Str + 121, Int + 5, Dex + 26, Spd + 13" ),
	array( "Silver crossbow", "Shiny Heavy", 0, 48000, "Str + 166, Int + 11, Dex + 39, Lck + 10, Spd + 18" ),
	array( "Gold crossbow", "Shinier Heavier", 0, 100000, "Str + 238, Int + 20, Dex + 61, Lck + 25, Spd + 32" ),
	array( "Dragon crossbow", "OH SHI-", 0, 400000, "Str + 650, Int + 36, Dex + 159, Lck + 46, Spd + 64" ),
	array( "Shiny stick", "It's stuck.", 0, 250, "HP * 0.95, TP * 1.05, Str + 4, Int + 5, SDf + 3, Dex + 1, Lck + 5" ),
	array( "Rainbow stick", "Multicoloured fun!", 0, 1390, "HP * 0.93, TP * 1.07, Str + 10, Int + 16, SDf + 11, Dex + 2, Lck + 15" ),
	array( "Wooden staff", "Ow, my back..", 0, 5200, "HP * 0.88, TP * 1.15, Str + 27, Def + 3, Int + 36, SDf + 29, Spd - 2" ),
	array( "Light staff", "Yagami?", 0, 18000, "HP * 0.85, TP * 1.21, Str + 67, Def + 5, Int + 77, SDf + 59, Spd - 3" ),
	array( "Sapphire staff", "Water Magic", 0, 45000, "HP * 0.81, TP * 1.30, Str + 97, Def + 6, Int + 126, SDf + 103, Spd - 4" ),
	array( "Ruby staff", "Fire Magic", 0, 95000, "HP * 0.79, TP * 1.46, Str + 119, Def + 9, Int + 179, SDf + 152, Spd - 6" ),
	array( "Crystal staff", "Easily Shattered Magic", 0, 210000, "HP * 0.75, TP * 1.80, Str + 161, Def + 12, Int + 260, SDf + 239, Spd - 8" ),
	array( "Dragon staff", "Dead Dragon.", 0, 480000, "HP * 0.70, TP * 2.29, Str + 216, Def + 15, Int + 438, SDf + 423, Spd - 15" ),
	array( "Dagger", "Short Stabbity", 0, 800, "TP + 3, Str + 25, Int + 2, Dex + 5, Lck + 1, Spd + 5" ),
	array( "Dirk", "Pitt", 0, 3400, "TP + 10, Str + 49, Int + 7, Dex + 13, Lck + 2, Spd + 13" ),
	//array( "Abracadabra", "Generic Wand. Does magical stuff. Will not accidentally backfire on you like Ron's does.", 0, 7845, "HP * 1.00, TP * 1.16, Int + 60" ),
	array( "Stilleto", "...oh...", 0, 10900, "TP + 27, Str + 77, Int + 19, Dex + 28, Lck + 4, Spd + 28" ),
	array( "Generification Wand", "What the hell, this isn't even magical! Increases code complexity, headaches, and attractiveness of alcohol.", 0, 15400, "HP * 1.17, TP * 1.00, Str + 165, Int - 265, Lck - 5" ),
	array( "Tsukasa's Wand", "Ludicrous World we live in. Using this item gives you great power, but traps you in the board. You may be able to call upon your guardian for assistance.", 0, 20020, "HP * 1.00, TP * 1.00, Int + 40, Dex + 120, Spd + 10" ),
	array( "Thief's Dagger", "What, does it grant +4 Sneak or something?", 0, 21300, "TP + 53, Str + 113, Int + 43, Dex + 51, Lck + 9, Spd + 51" ),
	array( "Kamek's Wand", "Useful for enlarging little things so they can devour babies", 0, 23000, "HP * 1.00, TP * 1.80, Int + 200" ),
	array( "Kris", "Wicked looking blade there, chap", 0, 44500, "HP + 100, TP + 100, Str + 200, Def + 50, Int + 50, Dex + 40, Spd + 40" ),
	array( "Fairy's Dagger", "Fair(l)y good.", 0, 100000, "TP + 145, Str + 220, Int + 183, SDf + 133, Dex + 113, Lck + 35, Spd + 113" ),
	array( "Demon Fangs", "Fangs for the memories~", 0, 220000, "HP + 30, TP + 222, Str + 430, Def - 30, Int + 233, Dex + 127, Lck + 15, Spd + 127" ),
	array( "Orichalcon", "Give that back, Isaac!", 0, 475000, "TP + 400, Str + 700, Def + 100, Int + 300, SDf + 100, Dex + 150, Lck + 25, Spd + 150" ),
	array( "Short Spear", "Short.", 0, 1200, "Str + 40" ),
	array( "Pike", "Long.", 0, 4800, "Str + 73" ),
	array( "Soldier Spear", "TONIGHT...", 0, 12000, "HP + 10, Str + 136, Def + 10, Int - 5, SDf - 5, Spd - 5" ),
	array( "Enchanted Rake", "Strong.", 0, 33000, "HP + 10, TP + 10, Str + 190, Def + 10, Int + 10, SDf + 10, Dex + 10, Lck + 10, Spd + 10" ),
	array( "Heavy Spear", "WE DINE...", 0, 68000, "Str + 323, Spd - 5" ),
	array( "Gugnir Lance", "IN HELL!", 0, 330000, "Str + 707, Spd - 10" ),
	array( "Holy Longinus", "Painful", 0, 600000, "HP + 50, TP + 50, Str + 1000, Def + 50, SDf + 50" ),
	array( "Mumei", "Sharp", 0, 1100, "HP * 0.98, TP + 1, Str + 43, Def - 5, Dex + 1, Spd + 2" ),
	array( "Kunishige", "Long, Sharp, Painful", 0, 3800, "HP * 0.95, TP + 2, Str + 80, Def - 12, Dex + 2, Spd + 5" ),
	array( "Kotetsu", "Long, Sharp, Painful", 0, 11500, "HP * 0.92, TP + 5, Str + 152, Def - 27, Dex + 5, Spd + 10" ),
	array( "Osafune", "Long, Sharp, Painful", 0, 29800, "HP * 0.88, TP + 10, Str + 257, Def - 43, Dex + 10, Spd + 20" ),
	array( "Magaroku", "Long, Sharp, Painful", 0, 57000, "HP * 0.82, TP + 15, Str + 413, Def - 72, Dex + 20, Spd + 40" ),
	array( "Masamune", "Long, Sharp, Painful", 0, 125000, "HP * 0.80, TP + 20, Str + 665, Def - 38, Dex + 40, Spd + 80" ),
	array( "Muramasa", "Long, Sharp, Painful", 0, 310000, "HP * 0.82, TP + 25, Str + 878, Def - 50, Dex + 64, Spd + 128" ),
	array( "Amenohabakiri", "Long, Sharp, Painful", 0, 550000, "HP * 0.85, TP + 30, Str + 1250, Def - 100, SDf + 100, Dex + 100, Spd + 200" ),
	array( "Crowbar", "Does that say Black Mesa on the side?", 0, 8500, "Str + 80" ),
	//array( "Staff of Stark", "The incredible magical power... What is this?", 0, 5000000, "HP * 3.00, TP + 6000, Str * 3.00, Int * 163.84, SDf + 7500, Dex * 15.00, Lck * 5.00, Spd * 8.00" ),
*/

	//ARMOR
	array( "Nothing", "Nothing. At All.", 1, 0, "" ),
	//array( "Phazon Suit", "Bears the mark of Chozo, outlined with phazon.", 1, 1534239, "HP + 32767, TP + 32767, Str + 4500, Def + 32767, Int + 25000, SDf + 32767, Dex + 12000, Lck * 15.00, Spd * 20.00" ),
	//array( "Laplace's Demon", "With the power of Newtonian physics, you too can know the position and velocity of every electron in the known universe! Oh wait, too bad it doesn't work for quantum physics. Oh well"!, 1, 3141597, "HP * 0.40, Def + 31416, Int * 2.78, SDf * 2.78, Dex * 2.78, Lck * 8.00, Spd * 15.71" ),
	array( "Clothes", "No Please, keep them on.", 1, 45, "Def + 3" ),
	array( "Cardboard box", ">:D", 1, 100, "Def * 2, Spd * 0.5", 13 ),

/*
	array( "Fur coat", "PETA is going to have a field day with this", 1, 180, "Def + 8, SDf + 1, Lck + 1" ),
	array( "Leather armor", "Dwarven Sized", 1, 650, "Def + 15, SDf + 1" ),
	array( "Copper armor", "Electrically conductive", 1, 2300, "Def + 34, SDf + 1, Dex - 1, Spd - 1" ),
	array( "Iron armor", "Metal. Heavy. Strong.", 1, 9500, "Def + 56, SDf + 2, Dex - 2, Spd - 3" ),
	array( "Ghoulish Rags", "Complete with ball and chains", 1, 14900, "Def + 156, Lck * 0.66, Spd * 0.33" ),
	array( "Semtex Vest", "On the plus side, nobody is <em>ever</em> going to come anywhere NEAR you.", 1, 25000, "HP * 0.05, Def + 500, Lck * 0.05" ),
	array( "Heavy armor", "Only Stravich could ever wear this.", 1, 26500, "Def + 152, Int - 5, SDf + 3, Dex - 10, Lck - 5, Spd - 13" ),
	array( "Spiked armor", "DON'T HIT THIS", 1, 32000, "Str + 84, Def + 127, Int - 17, Dex - 5, Spd - 24" ),
	array( "Ragged Cloak", "Great if you're stuck in a desert, out of place in high society.", 1, 38900, "Def + 230, Spd * 0.94" ),
	array( "Silver armor", "Shiny Protection", 1, 47000, "Def + 247, SDf + 14, Lck + 5, Spd - 15" ),
	array( "Cleric's robe", "Sorry, I don't think the Geneva Convention covers clerics...", 1, 57800, "TP + 110, Def + 20, Int + 5, SDf + 60, Spd - 20" ),
	array( "Biker's Leather Jacket", "Yeah, because you're so <em>infinitely</em> tough.", 1, 75800, "Str + 5, Def + 453, Int - 30, Lck * 0.75, Spd * 0.95" ),
	array( "Stone armor", "Is this some kind of joke?!", 1, 85000, "Str - 13, Def + 446, Int - 21, SDf + 35, Dex * 0.46, Lck * 0.53, Spd * 0.55" ),
	array( "Gold armor", "Perfect for target practice... and you'd be the target", 1, 129000, "Def + 373, SDf + 23, Lck + 12, Spd - 15" ),
	array( "Shell armor", "100% Pure Red Koopa", 1, 280000, "HP * 1.60, Def + 795, SDf + 326, Dex * 0.33, Lck + 121, Spd * 0.33" ),
	array( "Dragon armor", "..ew.", 1, 490000, "HP * 1.33, Str + 25, Def + 669, SDf + 165, Lck + 54" ),
	array( "Light robe", "Not heavy at all.", 1, 3500, "TP + 8, Def + 17, Int + 13, SDf + 12, Dex - 3" ),
	array( "Magician robe", "Magically enhanced for superior casting", 1, 15200, "HP * 0.96, TP * 1.12, Def + 39, Int + 44, SDf + 36, Dex - 5, Spd - 1" ),
	array( "Shining robe", "MY EYES", 1, 39000, "HP * 0.93, TP * 1.26, Def + 95, Int + 103, SDf + 97, Dex - 5, Lck + 20, Spd - 2" ),
	array( "Rainbow robe", "Straight from the 60's", 1, 109000, "HP * 0.95, TP * 1.38, Def + 167, Int + 182, SDf + 176, Lck * 2.00" ),
	array( "Crystal robe", "I would not want to wear this when it shatters", 1, 220000, "HP * 0.90, TP * 1.66, Def + 276, Int + 280, SDf + 269, Lck + 24, Spd * 0.90" ),
	array( "Dragon robe", "Dragon Lovers Lament", 1, 480000, "HP * 0.83, TP * 2.02, Str + 5, Def + 363, Int + 380, SDf + 376, Lck + 46" ),
	//array( "Overcoat Above Dreams", "Another dreamlike item, it is an expression of that which was taken formed into what Stark chooses it to be.", 1, 5000000, "Def * 5.00, Int + 2000, SDf + 1500, Lck * 6.67, Spd + 200" ),
*/

	//SHIELDS
	array( "Nothing", "Nothing. At All.", 2, 0, "" ),
	array( "Plastic plate", "Good for eating off of. Not so good as a shield.", 2, 40, "Def + 2", 14 ),
	array( "Magical shield", "You deflect more than rocks with this!", 2, 500, "Def + 20"),
	//array( "Wsithengamot", "Durable, but heavy", 2, 15000, "HP * 1.00, TP * 1.00, Def * 1.20, Dex - 150, Spd * 0.85" ),
	//array( "Fibonacci's Fractal", "It kinda looks like a sunflower when you look at it from afar...", 2, 8388607, "HP + 100, TP + 100, Str * 2.00, Def + 300, Int + 500, SDf + 800, Dex + 1300, Lck + 2100, Spd + 3400" ),
/*
	array( "Plastic plate", "Good for eating off of. Not so good as a shield.", 2, 40, "Def + 2" ),
	array( "Wooden plank", "I'm board", 2, 200, "Str + 4, Def + 5, Dex - 1, Lck - 1, Spd - 1" ),
	array( "Wood shield", "Good for defending against a wooden axe.", 2, 850, "Str + 2, Def + 12, Dex - 2, Spd - 2" ),
	array( "Iron shield", "Decent Protecting for a decent price", 2, 4800, "Str + 7, Def + 43, Dex - 4, Spd - 5" ),
	array( "Light shield", "Blindingly good.", 2, 6000, "HP + 3, TP + 3, Str + 2, Def + 45, Int + 2, Lck + 3" ),
	array( "Spiked shield", "Also good as a weapon.", 2, 23000, "Str + 86, Def + 75, Int - 16, Dex - 8, Lck - 6, Spd - 9" ),
	array( "Tower Shield", "Might be strong, but what, you thought you were going somewhere?", 2, 28950, "Def + 375, Spd - 600" ),
	array( "Silver shield", "Shiny Shield", 2, 30000, "Str + 13, Def + 145, SDf + 12, Dex - 5, Lck + 8, Spd - 9" ),
	array( "Woven Steel Teardrop", "A strong, relatively lightweight shield that also folds up into an airplane-friendly carrying bag!", 2, 37500, "Def + 155" ),
	array( "Gold shield", "Shinier shield", 2, 97000, "Str + 20, Def + 263, SDf + 34, Dex - 10, Lck + 29, Spd - 15" ),
	array( "Dragon shield", "Certainly strong enough", 2, 460000, "HP * 1.20, TP * 1.10, Str + 126, Def + 409, Int + 33, SDf + 169, Lck + 102" ),
	array( "Holoshield", "No idea where the Technology came from, but it's a worthwhile investment if you can afford it.", 2, 1025000, "HP * 1.40, Def * 5.00, SDf * 4.50, Spd + 50" ),
	//array( "Puzzle of Powerabuse", "...? What did Stark do!?", 2, 5000000, "HP + 4000, TP + 4000, Str + 4000, Def + 8000, Int + 7000, SDf + 32767, Dex + 32767, Lck + 10000, Spd + 10000" ),
*/

	//HELMS
	array( "Nothing", "Nothing. At All.", 3, 0, "" ),
	array( "Cunning hat", "Pretty cunning, innit?", 3, 1000, "Int * 1.05, Def + 20", 23 ),
	array( "French beret", "Zut alors?!", 3, 4500, "HP * 1.00, TP * 1.00, Def + 5, Int + 2, Lck + 50" ),
	array( "Trollface mask", "You get the picture&hellip; Right?", 3, 200, "Str * 1.8, Int * 0.6" ),
	array( "Tricorne", "You are a pirate!", 3, 500, "Dex * 2.2, Int * 0.5, Def + 50" ),
/*
	array( "Top Hat", "The ultimate in exquisite fashion. Sure to improve your confidence and luck with the ladies", 3, 12500, "HP * 1.00, TP * 1.10, Lck + 3" ),
	array( "Green Cap", "Wait up, Bro!", 3, 64000, "HP * 1.00, TP * 1.20, Int + 50, Dex + 20, Spd * 0.85" ),
	array( "Paper Hat", "Made out of newspaper. Used in elementary school playground games.", 3, 50, "Lck + 1" ),
	array( "Tinfoil Hat", "Shields you from government mind control. Also shields you from being regarded as a sane person.", 3, 460, "Def + 2, Int - 1, SDf + 1" ),
	array( "Baseball Cap", "A regular baseball cap, with an unintelligible logo on the back.", 3, 560, "Def + 50, Int - 5, Lck + 25" ),
	array( "Fedora Hat", "Runs Linux.", 3, 840, "Def + 3, Spd + 1" ),
	array( "Plumber's Hat", "What the @#$%, you took this off of Acmlm didn't you?!", 3, 1985, "HP * 1.00, TP * 1.00" ),
	array( "Steel-plated Party Hat", "The best precaution you can take if you want to party hard.", 3, 3100, "Str + 4, Def + 7, SDf + 1, Lck + 2" ),
	array( "Matter-Less Hat", "Super cool hat. Now 100% matter free for those allergic to anything but nothingness. No idea how it raises MP, but it does.", 3, 3975, "HP * 1.00, TP + 75" ),
	array( "Captain Falcon's Helmet", "Lots of speed, but watch out for Goroh! Oh, and this won't give you the punch, either.", 3, 4580, "HP * 1.00, TP * 1.00, Spd * 2.00" ),
	array( "Marisa's Hat", "She stole the precious thing.", 3, 7777, "HP * 1.00, TP * 1.10" ),
	array( "Gurren Lagann's Helmet", "Gurren-Lagann's helmet: Just make sure you stay away from Viral whilst wearing this.", 3, 12000, "HP * 1.50, TP * 1.00" ),
	array( "Kamina's Hatglasses", "There's something you don't see every day", 3, 5510, "Str + 50, Def + 5, Int - 120" ),
	//array( "Impossibly Long Hair", "Not actually a helmet, Stark's hair is soaked in the raw energy of dreams themselves.", 3, 5000000, "HP * 7.00, TP * 0.60, Def * 5.00, Int + 10096, SDf + 2400, Dex + 200, Lck * 2.00" ),
	//array( "Jedi training helmet", "Shields from distractive optical sensations (like post layouts) and allows the aspiring forum knight to focus his mind on the actual post contents.", 3, 991, "TP + 10, Def + 4, SDf + 15, Dex - 5" ),
*/

	//BOOTS
	array( "Nothing", "Nothing. At All.", 4, 0, "" ),
	//Clogs -- Authentic Dutch wooden clogs.
/*
	array( "Spike Stompers", "I feel sorry for anyone <em>stuck</em> under you", 4, 400, "HP * 1.00, TP * 1.00, Str * 1.25, Def * 1.05, Spd + 75" ),
	array( "High Jump Boots", "Sexy Chozo Technology", 4, 85000, "HP * 1.50, TP * 1.50, Str * 3.00, Int * 1.15, Dex * 2.00, Lck + 130, Spd + 60" ),
	array( "Boots ME", "I heard they were made in a week", 4, 50, "HP * 1.10, Int - 900, Lck - 1000" ),
	array( "Socks", "Great for DDR, not so great considering the needle you just stepped in", 4, 3400, "Int + 10, Dex + 7" ),
	array( "Slippers", "Warm and Comfy", 4, 4500, "Str + 5, Dex + 10, Spd - 5" ),
	array( "Rollerskates", "No brakes, just (bone) breaks.", 4, 9850, "HP * 1.10, TP * 0.90, Spd * 1.25" ),
	array( "Sneakers", "Squeaky.", 4, 12500, "Str + 50, Dex + 160, Spd * 0.98" ),
	array( "High-Heels", "Anklesnappers", 4, 14890, "Str + 25, Int + 5, Lck - 50" ),
	array( "Plumber's Boots", "Boi-oi-oing!", 4, 29850, "HP + 50, Str + 800, Lck + 333, Spd + 50" ),
	array( "Steel-toed Flip-flops", "Click, Click", 4, 75, "Def + 5, Spd - 1" ),
	array( "Nuclear Rocket Boots", "Einstein was right, oh shi-", 4, 15860, "Def * 0.50, Spd * 3.00" ),
	array( "Plated Low Boots", "Fancy futuristic boots, but apart from the price tag they're not all that great.", 4, 1078000, "Str + 235, Def + 45, Dex + 35, Spd + 90" ),
	//array( "Classy Dress Shoes", "Once an ordinary pair of dress shoes, it became saturated by Stark energy, evolving to its current form.", 4, 5000000, "Str + 2000, Def * 2.00, Int + 3000, SDf + 3500, Dex + 1000, Lck * 2.00, Spd + 3500" ),
*/

	//ACCESSOIRIES
	array( "Nothing", "Nothing. At All.", 5, 0, "" ),
	array( "Baguette", "ZUT ALORS!", 5, 50, "Str + 2, Int - 3", 16 ),
	array( "Cool shades", "Plain black, still very cool. <span style=\"color: #66ff66;\">&lt;!-- Reveals comments. --&gt;</span>", 5, 50000, "Dex + 50, Lck + 25", 18 ),
	array( "Power Limiter 2000", "Halves all stats for a fair chance", 5, 0, "HP / 2, TP / 2, Str / 2, Def / 2, Int / 2, SDf / 2, Dex / 2, Lck / 2, Spd / 2" ),
	array( "Power Limiter 3000", "Thirds all stats for a fair chance", 5, 0, "HP / 3, TP / 3, Str / 3, Def / 3, Int / 3, SDf / 3, Dex / 3, Lck / 3, Spd / 3" ),
	array( "Power Limiter 9000", "Ninths all stats for a fair chance", 5, 0, "HP / 9, TP / 9, Str / 9, Def / 9, Int / 9, SDf / 9, Dex / 9, Lck / 9, Spd / 9" ),
	array( "Catgirl ears", "So cute they're guaranteed to make people go \"KAWAII! ^_^\", or your money back!", 5, 10000, "Lck * 2", 17 ),
	array( "Mini Mushroom", "Well, at least you can fit into small places now.", 5, 100, "HP / 2, Str / 2, Spd * 4", 15 ),
	array( "Mega Mushroom", "These are taken on a daily basis by Mega-Mario.", 5, 100, "HP * 2.1, Str * 2.2, Spd * 0.8", 15 ),
	array( "N64", "OMFG, Mario in 3D (kind of)", 5, 199, "Int + 20"),
	array( "Das Keyboard", "Turns out you’ll type faster than you ever dreamed on one of these blank babies.", 5, 17500, "HP - 20, TP + 10, Int + 50, Spd + 15" ),

/*
	array( "Cane", "Nothing quite like it. Sure to make you look as distinquished and old as you aren't.", 5, 12500, "HP * 1.00, TP * 1.00, Str + 15, Spd + 5" ),
	//array( "That Which Was Taken", "Found in a dream, Stark used his power to force it into reality. What is this? Only Stark has gazed upon its true form.", 5, 8388607, "HP * 1.00, TP * 327.67, Def * 5.00, Int * 4.00, SDf * 200.00, Dex + 300, Spd * 20.00" ),
	array( "Mountain Dew", "Coding Fuel", 5, 8500, "HP + 25, Int + 25" ),
	array( "Scouter", "What does the scouter say about his powerlevel?", 5, 9001, "HP + 10, TP + 50, Int - 5, Lck - 20" ),
	array( "Yamaha", "Because everyone likes to make crappy music!", 5, 11500, "Int + 15, Spd - 5" ),
	array( "Ancient Tome", "Increases magical ability, but it's pretty darned big...", 5, 12450, "TP + 375, Dex - 225" ),
	array( "Carpenter's Belt", "Because sometimes you just want to look busy.", 5, 12500, "Str + 20, Dex + 35" ),
	array( "Keyboard", "TAKA TAKA TAKA", 5, 17500, "HP - 20, TP + 10, Int + 50, Spd + 15" ),
	array( "Laptop Computer", "Because we all like to look at our \"image\" collections on the go.", 5, 17500, "Int + 50, Lck + 5" ),
	array( "Boom Box", "For when you <em>really</em> like your music. Heavy, too.", 5, 23500, "HP + 25, Str + 65, Int - 15, Dex + 50, Lck - 25, Spd - 50" ),
	array( "Booze", "Hit the Ballmer's Peak and you'll be a super coder. Miss it and you're not driving yourself home tonight.", 5, 24000, "HP - 25, TP - 25, Def - 15, Int + 50, Lck + 100" ),
	array( "PHP", "The language of Champions", 5, 45000, "Int + 175" ),
	//array( "WXGA+ TFT display", "For when you really feel like you should be able to display larger images.", 5, 50000, "TP + 15, Str + 15, Def + 15, Int + 15" ),
	//array( "Goggles", "THEY DO NOTHING", 5, 25, "" ),
	//array( "Xkeeper's goggles", "<!-- Duh. -->", 5, 37800, "" ),
*/
);

$rUser = Query("select * from users left join users_rpg on users.id = users_rpg.id where users.id=".$loguserid);
$user = Fetch($rUser);
$stats = GetStats($user);
print_r($stats);
print "\n";

foreach($items as $item)
{
	$rawItem = array($item[0], $item[1], $item[2], $item[3], $item[4], $item[5]);
	print serialize($rawItem)."\n";
	$query = "insert into rpgitems values (NULL, '".justEscape($item[0])."', '".justEscape($item[1])."', ".(int)$item[2].", ".(int)$item[3].", '".justEscape($item[4])."', ".(int)$item[5].")";
	Query($query);
	//print $query."\n";
}

print "\n";
$stats = GetStats($user, 'a:5:{i:0;s:7:"Crowbar";i:1;s:0:"";i:2;i:0;i:3;i:8500;i:4;s:8:"Str + 80";}');
print_r($stats);

print "</pre>";

?>