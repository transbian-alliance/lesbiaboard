<?php

print "Counting post +1's...<br>";
Query("UPDATE {posts} p SET postplusones =
			(SELECT COUNT(*) FROM {postplusones} pp WHERE pp.post = p.id)
		WHERE 1");

print "Counting user +1's given...<br>";
Query("UPDATE {users} u SET postplusonesgiven =
			(SELECT COUNT(*) FROM {postplusones} pp WHERE pp.user = u.id)
		WHERE 1");

print "Counting user +1's received...<br>";
Query("UPDATE {users} u SET postplusones =
			(SELECT COUNT(*) FROM {postplusones} pp 
			LEFT JOIN {posts} p on pp.post = p.id
			WHERE p.user = u.id)
		WHERE 1");

