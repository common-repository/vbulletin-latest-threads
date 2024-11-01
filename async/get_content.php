<?php

if (!function_exists('add_action'))
{
  require_once("../../../../wp-config.php");
}
if (isset($vbulletin_lt)) {
	$vbulletin_lt->get_content();
}
?>
