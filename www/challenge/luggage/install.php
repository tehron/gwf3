<?php
chdir("../../");
require_once ("challenge/html_head.php");
if (!GWF_User::isAdminS()) {
	echo GWF_HTML::err('ERR_NO_PERMISSION');
	return;
}

$title = "Luggage";
$solution = false;
$score = 3;
$url = "challenge/luggage/index.php";
$creators = "MagicToast";
$tags = 'Encoding,Cracking,Math';
WC_Challenge::installChallenge($title, $solution, $score, $url, $creators, $tags, true);
require_once ("challenge/html_foot.php");
?>