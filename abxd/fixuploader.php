<?php
include "lib/common.php";
Query("update `uploader` set `date` = `id` where `date` = 0;");
Query("update `usercomments` set `date` = `id` where `date` = 0;");
?>