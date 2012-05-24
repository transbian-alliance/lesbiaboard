<?php

$postText = preg_replace('/^\s*&gt;.*/m', '<span style="color: green" class="implication">$0</span>', $postText);

?>