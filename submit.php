<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/

include('php/settings.php');
include('php/csrf.php');
include('php/Parsedown.php');
include('php/sqlite.php');

$Parsedown = new Parsedown();
$Parsedown->setSafeMode(true);

function tripcode($tripcode, $salt)
{
	if ($tripcode == '')
	{
		return '';
	}
	else
	{
		return '<input type="text" readonly value="' . hash('sha256', $tripcode . $salt) . '" class="tripcode">';
	}
}

// Redirect if some BS is going on
function redirectError($msg)
{
	$_SESSION['mtPostError'] = true;
	$_SESSION['mtPostErrorTxt'] = $msg;
	header('location: index.php');
	die(0);
}

if (! isset($_POST['text']) || ! isset($_POST['CSRF']) || ! isset($_POST['title']) || ! isset($_POST['name']) || ! isset($_POST['tripcode']))
{
	redirectError('Missing argument');
}

if ($_POST['CSRF'] != $_SESSION['CSRF'])
{
	redirectError('Invalid CSRF token');
}


if ($captcha)
{
	if (! isset($_SESSION['currentPosts']))
	{
		$_SESSION['currentPosts'] = $postsBeforeCaptcha;
	}
	if ($_SESSION['currentPosts'] >= $postsBeforeCaptcha)
	{
		if (! isset($_POST['captcha']))
		{
			redirectError('Invalid captcha');
		}
		if ($_POST['captcha'] != $_SESSION['captchaVal'])
		{
			redirectError('Invalid captcha');
		}
		else
		{
			$_SESSION['currentPosts'] = 0;
		}
	}
}


// Get user data

$text = $_POST['text'];
$title = $_POST['title'];
$name = $_POST['name'];
$tripcode = $_POST['tripcode'];

$name = $db->escapeString($name);

if (strlen($_POST['text']) > 100000 || strlen($_POST['title'] > 20) || strlen($_POST['name'] > 20) || strlen($_POST['tripcode'] > 100))
{
	redirectError('Text, title, name, or tripcode is too long.');
}

// html encode user data to prevent xss
$text = htmlspecialchars($text);

if (stripos($text, 'javascript:') !== false){
	redirectError('Contains illegal string, javascript link.');
}
if (stripos($text, 'data:') !== false){
	redirectError('Contains illegal string, data link.');
}
if (stripos($text, 'blob:') !== false){
	redirectError('Contains illegal string, blob link.');
}

$text = $Parsedown->text($text);

$title = str_replace('\\', '', $title);
$title = str_replace('/', '', $title);
$title = str_replace('\\', '', $title);
$title = str_replace('#', '', $title);
$title = str_replace('&', '', $title);
$title = str_replace('"', '', $title);
$title = str_replace('\'', '', $title);
$title = str_replace('>', '', $title);
$title = str_replace('<', '', $title);

if (strstr($title, '.')){
	if ($title[0] != "."){
		$title = str_replace('.', '', $title);
	}
}

if ($title == ''){
	redirectError('Title cannot be blank');
}

$title = htmlspecialchars(SQLite3::escapeString($title));
$title = rtrim(ltrim($title));

if (! $allowHidden) {
	$title = ltrim($title, '.');
}
$name = htmlspecialchars($name);

if (file_exists('posts/' . $title . '.html')){
	redirectError('A post by that title already exists');
}


// Make the new post file


if (!file_exists('posts/')) {
    mkdir('posts/');
}

// Insert into thread list DB

$sql =<<<EOF
	 INSERT INTO threads (title, author)
	 VALUES ('$title', '$name');
EOF;


$ret = $db->exec($sql);
if(!$ret){
	die($db->lastErrorMsg());
}
$db->close();


$postFile = 'posts/' . $title . '.html';

$postID = time();

$doctype = '<!DOCTYPE HTML>';
$compiled =  $doctype . '<div class="title">' . $title . '</div><div class="name">' . $name . '</div>' . tripcode($tripcode, $salt) . ' <div class="postID" id="OP" onClick="javascript: clickItem(\'OP\');">' . $postID . '</div> <div class="post" id="post">' . $text . '</div><span id="' . $postID . '"></span>';


file_put_contents($postFile, $compiled, LOCK_EX);

if ($captcha) {
	$_SESSION['currentPosts'] = $_SESSION['currentPosts'] + 1;
}

header('location: view.php?post=' . $title);

?>
